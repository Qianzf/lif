<?php

namespace Lif\Core\Abst;

abstract class Model
{
    protected $name = null;    // table name
    protected $_tbx = null;    // table prefix
    protected $_fdx = null;    // field prefix
    protected $pk   = null;    // primary key

    protected $unwriteable = [];    // protected fields that cann't update
    protected $unreadable  = [];    // protected fields that cann't read

    // Stack for __set()
    protected $_fields = [
    ];
    // Stack for __get()
    protected $fields = [
    ];

    public function __construct($id = null)
    {
        $this->fields['id'] = ($this->pk = $id);
    }

    public function __get($field)
    {
        return $this->fields[$field] ?? null;
    }

    public function __set($field, $value)
    {
        $this->_fields[$field] = $value;

        return $this;
    }

    // If record exists then update or create
    protected function save()
    {
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
