<?php

// -----------------------------------------
//     MySQL table column abstract layer
// -----------------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

use Lif\Core\Intf\{SQLSchemaWorker, SQLSchemaBuilder};

class AbstractColumn implements SQLSchemaWorker
{
    private $creator   = null;
    private $alter     = null;
    private $name      = null;
    private $old       = null;
    private $concrete  = null;    // Concrete Column object
    private $concretes = [];
    private $callbacks = [
        'engine',
        'charset',
        'collate',
        'autoincre',
        'comment',
    ];

    public function ofCreator(SQLSchemaBuilder $creator) : SQLSchemaBuilder
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() : SQLSchemaBuilder
    {
        return $this->creator;
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

    private function setOld(string $name = null): AbstractColumn
    {
        $this->old = $name;

        return $this;
    }

    public function add(string $name)
    {
        return $this
        ->setAlter('add column')
        ->setName($name);
    }

    public function change(string $old, string $new)
    {
        return $this
        ->setAlter('change column')
        ->setOld($old)
        ->setName($new);
    }

    public function modify(string $name)
    {
        return $this
        ->setAlter('modify column')
        ->setName($name);
    }

    public function drop(string $name)
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

    public function renameTableAs(string $as)
    {
        return $this->renameTable($as, 'AS');
    }

    public function renameTableTo(string $to)
    {
        return $this->renameTable($to, 'TO');
    }

    public function renameTable(string $new, string $rename = 'TO')
    {
        return $this->concretes[] = (new class($new, $rename) {
            private $new    = null;
            private $rename = null;

            public function __construct(string $new, string $rename)
            {
                $this->new    = $new;
                $this->rename = strtoupper($rename);
            }
            public function grammar()
            {
                return "RENAME {$this->rename} `{$this->new}`";
            }
        });
    }

    public function setDefault(string $column, $default)
    {
        return $this->concretes[] = (new class($column, $default) {
            private $column  = null;
            private $default = null;

            public function __construct(string $column, $default)
            {
                $this->column  = $column;
                $this->default = $default;
            }
            public function grammar()
            {
                return "ALTER `{$this->column}` SET DEFAULT {$this->default}";
            }
        });
    }

    public function dropDefault(string $column)
    {
        return $this->concretes[] = (new class($column) {
            private $column  = null;

            public function __construct(string $column)
            {
                $this->column  = $column;
            }
            public function grammar()
            {
                return "ALTER `{$this->column}` DROP DEFAULT";
            }
        });
    }

    public function __call($name, $params)
    {
        if (in_array($name, $this->callbacks)) {
            call_user_func_array([$this->creator, $name], $params);

            return $this;
        }

        return call_user_func_array([
            ($this->concretes[] = $this->concrete()),
            $name
        ],
            $params
        );
    }

    public function getConcretes()
    {
        return $this->concretes;
    }

    private function concrete() : ConcreteColumn
    {
        return ($this->concrete = new ConcreteColumn)
        ->ofCreator($this)
        ->setName($this->name)
        ->setOld($this->old)
        ->setAlter($this->alter);
    }

    public function fulfillWishFor(SQLSchemaWorker $worker = null)
    {
        return $this->creator->fulfillWishFor($worker);
    }

    public function beforeDeath(SQLSchemaWorker $worker = null)
    {
    }
}
