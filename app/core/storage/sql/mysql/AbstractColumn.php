<?php

// -----------------------------------------
//     MySQL table column abstract layer
// -----------------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

class AbstractColumn
{
    private $table     = null;
    private $concretes = [];
    private $callback  = [
        'engine',
        'charset',
        'collate',
        'autoincre',
        'comment',
    ];

    public function ofTable(Table $table) : AbstractColumn
    {
        $this->table = $table;

        return $this;
    }

    public function __call($name, $params)
    {
        if (in_array($name, $this->callback)) {
            call_user_func_array([$this->table, $name], $params);

            return $this;
        }

        return call_user_func_array([
            ($this->concretes[] = (new ConcreteColumn)->ofCreator($this)),
            $name
        ],
            $params
        );
    }

    public function getConcretes()
    {
        return $this->concretes;
    }
}
