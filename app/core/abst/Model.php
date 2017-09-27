<?php

// ------------------------
//     Model/ORM of LiF
// ------------------------

namespace Lif\Core\Abst;

abstract class Model
{
    protected $table = null;    // table name
    protected $_tbx  = null;    // table prefix
    protected $_fdx  = null;    // field prefix
    protected $pk    = null;    // primary key
    protected $query = null;    // LDO query object

    protected $unwriteable = [];    // protected fields that cann't update
    protected $unreadable  = [];    // protected fields that cann't read

    // Stack of current query result
    protected $fields = [];
    protected $attrs  = [];

    public function __construct($id = null)
    {
        if ($id) {
            $this->pk = $this->pk ?? 'id';

            $this->fields = $this->query()->where(
                $this->pk,
                $id
            )->first();

            $this->attrs['where'] = '((`'.$this->pk.'` = ?))';
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

    // If record exists then update or create
    public function save(array $data = [])
    {
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

            return $this->table = defaultTableName;
        }

        return $this->table;
    }

    public function pk()
    {
        return $this->pk ?? 'id';
    }

    // ---------------------------------------------------------------
    //     $class => The model class belongs to this model
    //     $type  => 1: has many; 2: belongs to
    //     $lk    => Local key, primary key of this table
    //     $fk    => Foreign key, primary key of join table
    //     $cond  => Join string between foreign key and local key
    //     $lv    => Local value mapping to locak key
    // ---------------------------------------------------------------
    protected function join(
        string $model,
        int $type,
        string $lk = null,
        string $fk = null,
        string $cond = '=',
        $lv = null
    ) {
        $model   = model($model);
        $selects = $model->table.'.*';
        $lk = ($lk ?? 'id');
        $fk = ($fk ?? 'id');
        $fetch = (1 === $type) ? 'get' : 'first';
        $where = $this->table.'.'.$lk;

        if ($lv) {
            if (is_array($lv)) {
                $value = '('.implode(',', $lv).')';
            } else {
                $value = (string) $lv;
            }
        } else {
            // Use local key mapped value of current model
            $value = $this->fields[$lk] ?? null;
        }

        return $model
        ->select($selects)
        ->leftJoin(
            $this->table,
            $model->table.'.'.$fk,
            $cond,
            $where
        )
        ->where($where, $value)
        // ->_sql();
        ->$fetch();
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
    public function hasMany(
        string $model,
        string $lk = null,
        string $fk = null,
        string $cond = '=',
        $lv = null
    ) {
        return $this->join(
            $model,
            1,
            $lk,
            $fk,
            $cond,
            $lv
        );
    }

    // Get one specific model current model belongs to only
    // One-to-One
    public function belongsTo(
        string $model,
        string $lk = null,
        string $fk = null,
        string $cond = '=',
        $lv = null
    ) {
        return $this->join(
            $model,
            2,
            $lk,
            $fk,
            $cond,
            $lv
        );
    }
}
