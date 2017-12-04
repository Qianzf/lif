<?php

// ------------------------
//     Model/ORM of LiF
// ------------------------

namespace Lif\Core\Abst;

use Lif\Core\Storage\SQL\Builder;

abstract class Model extends \Lif\Core\Abst\Facade implements \ArrayAccess
{   
    use \Lif\Core\Traits\DI;

    // Child class confinable
    protected $table  = null;       // table name
    protected $alias  = null;       // table alias
    protected $_tbx   = null;       // table prefix
    protected $_fdx   = null;       // field prefix
    protected $pk     = null;       // primary key
    protected $rules  = [];         // validation rules for items
    protected $unwriteable = [];    // protected items that cann't update
    protected $unreadable  = [];    // protected items that cann't read

    // Stacks for current query
    private $items  = [];
    private $query  = null;
    private $alive  = false;
    private $filter = [];

    public function __construct(int $id = null, string $pk = null)
    {
        $this->pk = $pk ?? ($this->pk ?? 'id');
        if ($this->id = $id) {
            $this->find($this->id);
        }
    }

    // Find model via primary key
    public function find($pk = null)
    {
        $_pk = $this->pk();

        if (!is_scalar($pk) || is_null($pk)) {
            excp('Con not find model without legal primary key.');
        }

        if ($this->alive) {
            $__pk = $this->items[$_pk] ?? false;
            if ($pk == $__pk) {
                return $this;
            }
            $model = clone $this;
        } else {
            $model = $this;
        }

        $model->items = $model
        ->query()
        ->where($_pk, $pk)
        ->first();

        if ($model->items) {
            $model->alive = true;

            return $model;
        }
    }

    public function query(string $conn = null) : Builder
    {
        if (!$this->query || !($this->query instanceof Builder)) {
            $this->query = db($conn)
            ->table(
                $this->getTable(),
                $this->getAlias()
            );
        }

        return $this
        ->query
        ->persistentFrom($this->filter);
    }

    public function pk()
    {
        return $this->pk ?? 'id';
    }

    public function __get(string $key)
    {
        return $this->items[$key] ?? null;
    }

    public function __unset($key): void
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
    }

    public function __set($item, $value)
    {
        $this->items[$item] = $value;

        return $this;
    }

    public function __call($name, $args)
    {
        $res = call_user_func_array([
                $this->query(),
                $name
            ],
            $args
        );

        if ($res instanceof Builder) {      
            $this->filter = $res->persistentFor();
            $this->query  = $res;

            return $this;
        }

        if (is_array($res)) {
            if (! $res) {
                return null;
            }
            if (! isset($res[0])) {
                // If only one result
                // Then return current model instance itself
                $this->items = $res;

                return $this;
            }

            $this->__toModel($res);
        }

        if (('delete' == $name) && $res) {
            $this->reset();
        }

        return $res;
    }

    // Get readable model data
    public function items() : array
    {
        $items = $this->items;

        foreach ($this->unreadable as $unreadable) {
            unset($items[$unreadable]);
        }

        return $items;
    }

    protected function __toModel(array &$data) : Model
    {
        array_walk($data, function (&$item, $key) {
            $model = clone $this;
            $model->items = $item;
            $model->alive = !empty_safe($item);
            $item = $model;
        });

        unset($item);

        return $this;
    }

    public function __toString()
    {
        return _json_encode($this->items());
    }

    public function all(bool $model = true, bool $persistent = true)
    {
        $query = $persistent ? $this : (clone $this);

        $res = $query->query()->get();

        if ($model) {
            $this->__toModel($res);
        }

        return $res;
    }

    public function create(
        array $data,
        bool $validate = true,
        array $rules = []
    )
    {
        return $this->reset()->save($data, $validate, $rules);
    }

    // If record exists then update or create
    // @return:
    // - string => validation error
    // - integer over 0 => success
    // - other  => failed
    // - null   => nothing happend
    public function save(
        array $data = [],
        bool $validate = true,
        array $rules = []
    )
    {
        if ($data = ($data ? $data : $this->items)) {
            if ($validate && ($rules = ($rules ?: ($this->rules ?: [])))) {
                if (true !== ($err = validate($data, $rules))) {
                    return $err;
                }
            }
            
            unset($data[$this->pk()]);    // Protected primary key

            $status = $this->alive
            ? $this
            ->query()
            ->where($this->pk, $this->items[$this->pk])
            ->update($data)

            : $this->query()->insert($data);

            $pk = $this->alive ? $this->items[$this->pk] : $status;

            if ($status > 0) {
                $this->items = $this
                ->query()
                ->reset()
                ->table($this->table)
                ->where($this->pk, $pk)
                ->first();
            }

            return $status;
        }

        return true;
    }

    // Empty model items
    public function empty() : Model
    {
        $this->items = [];

        return $this;
    }

    // Clean references
    public function clean() : Model
    {
        $this->query  = null;
        $this->filter = [];

        return $this;
    }

    // Clear status
    public function clear() : Model
    {
        $this->alive = null;

        return $this;
    }

    public function reset() : Model
    {
        $this->clean()->clear()->empty();

        return $this;
    }

    public function __clone()
    {
        $this->reset();

        return $this;
    }

    public function setAlias(string $alias) : Model
    {
        if (is_numeric($alias)) {
            excp('Table alias can not be a numberic.');
        }

        $this->alias = $alias ?: null;

        return $this;
    }

    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    public function isAlive()
    {
        return $this->alive;
    }

    public function getAlias()
    {
        $alias = empty_safe($this->alias)
        ? null
        : $this->alias;

        if (is_numeric($alias)) {
            excp('Table alias can not be a numberic.');
        }

        return $alias;
    }

    public function getPK()
    {
        return $this->items[$this->pk] ?? null;
    }

    public function getTable()
    {
        if (empty_safe($this->table)) {
            $defaultTableName   = (
                new \ReflectionClass($this)
            )->getShortName();

            $this->table = strtolower($defaultTableName);
        }

        return $this->table;
    }

    // ---------------------------------------------------------------
    //     $params:
    //     model => The model class belongs to this model
    //     type  => 1: has many; 2: belongs to
    //     lk    => Local key, primary key of this table
    //     fk    => Foreign key, primary key of join table
    //     cond  => Join string between foreign key and local key
    //     lv    => Local value mapping to loca key
    // ---------------------------------------------------------------
    protected function join(array $params) {
        $lk = $params['lk'] ?? 'id';
        if (isset($params['lv'])) {
            $value = is_array($params['lv'])
            ? '('.implode(',', $params['lv']).')'
            : ((string) ($params['lv']));
        } else {
            // Use local key mapped value of current model
            if (! isset($this->items[$lk])) {
                excp('Non-exists model can not has any relationship.');
            }

            $value = $this->items[$lk];
        }

        $model   = model($params['model']);
        $fk      = $params['fk'] ?? 'id';
        $cond    = $params['cond'] ?? '=';
        $selects = $model->getTable().'.*';
        $where   = $this->getTable().'.'.$lk;
        $oneonly = isset($params['type']) && (1 === $params['type']);
        $fetch   = $oneonly ? 'get' : 'first';

        $model = $model
        ->select($selects)
        ->leftJoin(
            $this->getTable(),
            $model->getTable().'.'.$fk,
            $cond,
            $where
        )
        ->where($where, $value);

        if ($fwhere = ($params['fwhere'] ?? null)) {
            foreach ($fwhere as $fkey => $fval) {
                $model = $model->where("{$model->getTable()}.{$fkey}", $fval);
            }
        }
        if ($lwhere = ($params['lwhere'] ?? null)) {
            foreach ($lwhere as $lkey => $lval) {
                $model = $model->where("{$this->getTable()}.{$lkey}", $lval);
            }
        }

        if (isset($params['sort'])
            && is_array($params['sort'])
            && $params['sort']
        ) {
            $model = call_user_func_array([$model, 'sort'], [$params['sort']]);
        }

        if (! $oneonly) {
            legal_or($params, [
                'from' => ['int|min:1', 0],
                'take' => ['int|min:1', 20],
            ]);

            $model = $model->limit(
                $params['from'],
                $params['take']
            );
        }
        
        return call_user_func_array([$model, $fetch], []);
    }

    // Get all associated models with current model
    // Many-to-Many
    public function withIn(
        string $model,
        string $lk = null,
        string $fk = null,
        string $cond = '=',
        $lv = null
    ) {
        // TODO
        // Use middle table to transform many-to-many
        // Into double One-to-many
    }

    // Get all related models of current model
    // One-to-Many
    public function hasMany(...$params) {
        return $this->parseRelation(
            'has-many',
            $params, function (&$params) {
                $params['type'] = 1;
            }
        );
    }

    // Get one specific model current model belongs to only
    // One-to-One
    public function belongsTo(...$params)
    {
        $model = $this->parseRelation(
            'belongs-to',
            $params, function (&$params) {
                $params['type'] = 2;
            }
        );

        if ($model) {
            $model->alive = true;
        }
        
        return $model;
    }

    public function parseRelation(
        string $relation,
        array $params,
        \Closure $callback
    ) {
        if (isset($params[0])) {
            // !!! Parameter index order will affect result here
            // 2 => Local key
            $idxMap = [
                0 => 'model',    // Model class namespace
                1 => 'lk',       // Foreign key
                2 => 'fk',       // Local key
                3 => 'lv',       // Local value mapping to local key
                4 => 'from',     // Limit start
                5 => 'take',     // Limit offset
                6 => 'sort',     // Sort rules => array
                7 => 'lwhere',   // Where conditions of local model => array
                8 => 'fwhere',   // Where conditions of foreign model => array
            ];

            if (is_array($params[0])) {
                foreach ($params[0] as $key => $value) {
                    // pr($key, isset($idxMap[$key]), in_array($key, $idxMap));
                    if (isset($idxMap[$key])) {
                        $_params[$idxMap[$key]] = $value;
                    } elseif (in_array($key, $idxMap)) {
                        $_params[$key] = $value;
                    } else {
                        excp("Illegal {$relation} parameters(2)");
                    }
                }
            } elseif (is_string($params[0])) {
                foreach ($params as $key => $value) {
                    $_params[$idxMap[$key]] = $value;
                }
            }

            if (!isset($_params['model'])
                || !isset($_params['lk'])
                || !isset($_params['fk'])
            ) {
                excp("Illegal {$relation} parameters(3)");
            }

            $callback($_params);

            return $this->join($_params);
        }

        excp("Illegal {$relation} parameters(1)");
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    protected static function getProxy()
    {
        return static::class;
    }
}
