<?php

namespace Lif\Core\Abst;

abstract class Model
{
    protected $conn  = null;
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
            $pk = $this->pk ?? 'id';

            $this->fields[$pk] = $id;
        }
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
            }

            $this->fields = $res;

            return $this;
        }

        return $res;
    }

    public function items() : array
    {
        return $this->fields;
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

    public function reset(): Model
    {
        $this->attrs = [];

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
            $defaultTableName = (new \ReflectionClass($this))
            ->getShortName();

            return $defaultTableName;
        }

        return $this->table;
    }

    // -------------------------------------------------------
    //     $class => The model class belongs to this model
    //     $fk    => Foreign key, primary key if not set
    //     $lk    => Local key, primary key if not set
    // -------------------------------------------------------
    protected function hasMany($class, $fk = null, $lk = null)
    {
    }

    // -------------------------------------------------------
    //     $class => The model class which owns this model
    //     $fk    => Foreign key, primary key if not set
    //     $lk    => Local key, primary key if not set
    // -------------------------------------------------------
    protected function belongsTo($class, $fk = null, $lk = null)
    {
    }
}
