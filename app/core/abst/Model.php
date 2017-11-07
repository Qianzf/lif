<?php

// ------------------------
//     Model/ORM of LiF
// ------------------------

namespace Lif\Core\Abst;

abstract class Model
{
    protected $table  = null;    // table name
    protected $_tbx   = null;    // table prefix
    protected $_fdx   = null;    // field prefix
    protected $pk     = null;    // primary key
    protected $query  = null;    // LDO query object
    protected $unwriteable = [];    // protected fields that cann't update
    protected $unreadable  = [];    // protected fields that cann't read

    // Stack of current query result
    protected $fields = [];
    protected $attrs  = [];
    protected $rules  = [];

    public function __construct($id = null)
    {
        if ($id) {
            $this->pk     = $this->pk ?? 'id';
            $this->fields = $this->query()->where(
                $this->pk,
                $id
            )->first();

            if (! $this->fields) {
                $this->reset();
            } else {
                $this->attrs['where'] = '((`'.$this->pk.'` = ?))';
            }
        }
    }

    // Find model via primary key
    public function find($pk)
    {
        $this->fields = $this
        ->query()
        ->where($this->pk(), $pk)
        ->first();

        return $this->fields ? $this : null;
    }

    public function __get($key)
    {
        return $this->$key ?? (
            $this->fields[$key] ?? null
        );
    }

    public function __unset($key): void
    {
        if (isset($this->fields[$key])) {
            unset($this->fields[$key]);
        }
    }

    public function __set($field, $value)
    {
        $this->fields[$field] = $value;

        return $this;
    }

    public function __clone()
    {
        $this->reset();

        return $this;
    }

    public function __call($name, $args)
    {
        $res = call_user_func_array(
            [
                $this->query(),
                $name
            ],
            $args
        );

        if (is_object($res)) {
            if (method_exists($res, 'getAttrsForModel')) {
                $this->attrs = $res->getAttrsForModel();
            }

            $this->query = $res;

            return $this;
        } elseif (is_array($res)) {
            if (! $res) {
                return null;
            } elseif (! isset($res[0])) {
                // If only one result
                // Then return current model instance itself
                $this->fields = $res;
                return $this;
            } else {
                $this->__toModel($res);
                return $res;
            }
        }

        if (('delete' == $name) && $res) {
            $this->reset();
        }

        return $res;
    }

    // Get public user data
    public function data(): array
    {
        $data = $this->fields;

        foreach ($this->unreadable as $key) {
            if (isset($data[$key]) || is_null($data[$key])) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    // Get all user data
    public function items() : array
    {
        return $this->fields;
    }

    protected function __toModel(array &$data) : Model
    {
        array_walk($data, function (&$item, $key) {
            $model = clone $this;
            $model->fields = $item;
            $item = $model;
        });

        unset($item);

        return $this;
    }

    public function all()
    {
        $res = $this->query()->get();

        $this->__toModel($res);

        return $res;
    }

    public function create(array $data, array $rules = [])
    {
        return $this->save($data, $rules);
    }


    // If record exists then update or create
    // @return:
    // - string => validation error
    // - integer over 0 => success
    // - other => failed
    public function save(array $data = [], array $rules = [])
    {
        if ($rules = ($rules ?: ($this->rules ?: []))) {
            if (true !== ($err = validate($data, $rules))) {
                return $err;
            }
        }

        if (! $data) {
            $data = $this->fields;
        }

        if (isset($this->pk) && $this->pk) {
            unset($data[$this->pk]);
        } else {
            unset($data['id']);
        }

        if (isset($this->attrs['where'])) {
            return $this->query()->update($data);
        }

        $lastInserstId = $this->query()->insert($data);

        return $this->fields[$this->pk] = $lastInserstId;
    }

    public function empty() : Model
    {
        $this->fields = [];

        return $this;
    }

    public function clear() : Model
    {
        $this->attrs = [];

        return $this;
    }

    public function clean() : Model
    {
        $this->query = null;

        return $this;
    }

    public function reset() : Model
    {
        $this->clean()->clear()->empty();

        return $this;
    }

    protected function query()
    {
        if (! $this->query || !is_object($this->query)) {
            $this->query = db($this->conn)
            ->table(
                $this->__table()
            );
        }

        if (isset($this->fields[$this->pk])) {
            $this->query = $this->query->where(
                $this->pk,
                $this->fields[$this->pk]
            );
        }

        return $this->query->setAttrsForModel($this->attrs);
    }

    protected function __table()
    {
        if (! $this->table) {
            $defaultTableName   = (
                new \ReflectionClass($this)
            )->getShortName();

            return $this->table = strtolower($defaultTableName);
        }

        return $this->table;
    }

    public function pk()
    {
        return $this->pk ?? 'id';
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
            if (! isset($this->fields[$lk])) {
                excp('Non-exists model can not has any relationship.');
            }

            $value = $this->fields[$lk];
        }

        $model   = model($params['model']);
        $fk      = $params['fk'] ?? 'id';
        $cond    = $params['cond'] ?? '=';
        $selects = $model->table.'.*';
        $where   = $this->table.'.'.$lk;
        $oneonly = isset($params['type']) && (1 === $params['type']);
        $fetch   = $oneonly ? 'get' : 'first';

        $model = $model
        ->select($selects)
        ->leftJoin(
            $this->table,
            $model->table.'.'.$fk,
            $cond,
            $where
        )
        ->where($where, $value);

        if (isset($params['sort'])
            && is_array($params['sort'])
            && $params['sort']
        ) {
            $model = call_user_func_array([$model, 'sort'], [$params['sort']]);
        }

        if (! $oneonly) {
            legal_or($params, [
                'take_from' => ['int|min:1', 0],
                'take_cnt'  => ['int|min:1', 20],
            ]);

            $model = $model->limit(
                $params['take_from'],
                $params['take_cnt']
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
        if (isset($params[0])) {
            if (is_array($params[0])) {
                $params[0]['type'] = 1;

                return $this->join($params[0]);
            } elseif (is_string($params[0])) {
                // !!! Parameter index order will affect result here
                // 2 => Local key
                $idxMap = [
                    0 => 'model',    // Model class namespace
                    1 => 'lk',       // Foreign key
                    2 => 'fk',       // Local key
                    3 => 'lv',       // Local value mapping to local key
                    4 => 'take_from',    // Limit start
                    5 => 'take_cnt',     // Limit offset
                    6 => 'sort'          // Sort rules => array
                ];
                $_params['type'] = 1;
                foreach ($params as $key => $value) {
                    if (! isset($idxMap[$key])) {
                        excp('Illegal has-many parameters.');
                    }

                    $_params[$idxMap[$key]] = $value;
                }

                return $this->join($_params);
            }
        }

        excp('Illegal has-many params.');
    }

    // Get one specific model current model belongs to only
    // One-to-One
    public function belongsTo(...$params) {
        if (isset($params[0])) {
            if (is_array($params[0])) {
                $params[0]['type'] = 2;

                return $this->join($params[0]);
            } elseif (is_string($params[0])) {
                // !!! Parameter index order will affect result here
                // 2 => Local key
                $idxMap = [
                    0 => 'model',    // Model class namespace
                    1 => 'lk',       // Foreign key
                    2 => 'fk',       // Local key
                    3 => 'lv',       // Local value mapping to local key
                ];
                $_params['type'] = 2;
                foreach ($params as $key => $value) {
                    if (! isset($idxMap[$key])) {
                        excp('Illegal belongs-to parameters.');
                    }

                    $_params[$idxMap[$key]] = $value;
                }

                return $this->join($_params);
            }
        }

        excp('Illegal belongs-to params.');
    }
}
