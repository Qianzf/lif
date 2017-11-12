<?php

// -----------------------------------------
//     MySQL table column abstract layer
// -----------------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

class AbstractColumn
{
    private $table     = null;
    private $alter     = null;
    private $name      = null;
    private $concretes = [];
    private $callbacks = [
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

    private function setAlter(string $alter = null): AbstractColumn
    {
        $this->alter = $alter;

        return $this;
    }

    private function setName(string $name = null): AbstractColumn
    {
        $this->name = $name;

        return $this;
    }

    public function addColumn(string $name)
    {
        return $this
        ->setAlter('add column')
        ->setName($name);
    }

    public function modifyColumn(string $name)
    {
        return $this
        ->setAlter('modify column')
        ->setName($name);
    }

    public function dropColumn(string $name)
    {
        return $this->concretes[] = (new class($name) {
            private $name = null;

            public function __construct(string $name)
            {
                $this->name = $name;
            }
            public function grammar()
            {
                return "DROP COLUMN `{$this->name}`";
            }
        });
    }

    public function __call($name, $params)
    {
        if (in_array($name, $this->callbacks)) {
            call_user_func_array([$this->table, $name], $params);

            return $this;
        }

        return call_user_func_array([
            ($this->concretes[] = $this->createColumn()),
            $name
        ],
            $params
        );
    }

    public function getConcretes()
    {
        return $this->concretes;
    }

    private function createColumn() : ConcreteColumn
    {
        return (
            new ConcreteColumn
        )
        ->ofCreator($this)
        ->setName($this->name)
        ->setAlter($this->alter);
    }
}
