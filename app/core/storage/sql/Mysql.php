<?php

// ----------------------------
//     MySQL schema builder
// ----------------------------

namespace Lif\Core\Storage\SQL;

use Lif\Core\Storage\SQL\Mysql\Table;
use Lif\Core\Intf\{SQLSchemaWorker, SQLSchemaBuilder};

class Mysql implements SQLSchemaWorker
{
    private $creator = null;
    private $name    = null;
    private $charset = 'utf8m4';
    private $collate = 'utf8m4_unicode_ci';

  //   [DEFAULT] CHARACTER SET [=] charset_name
  // | [DEFAULT] COLLATE [=] collation_name

    public function createDBIfNotExists(
        string $name,
        \Closure $callable = null
    )
    {
        return $this->__createDB($name, $callable, true);
    }

    private function __createDB(
        string $name,
        \Closure $callable = null,
        bool $check = false
    )
    {
        return $this->database('create', $name, $check);
    }

    public function createDB(
        string $name,
        \Closure $callable = null,
        bool $check = false
    )
    {
        return $this->__createDB($name, $callable, $check);
    }

    private function database(
        string $action,
        string $name,
        bool $check = false
    )
    {
        if (! ($action = strtoupper($action))) {
            excp(
                'Missing or illegal database manipulation: '
                .($this->name ?? '(empty)')
            );
        }
        if (! ($this->name = $name)) {
            excp(
                'Missing or illegal database name to create: '
                .($this->name ?? '(empty)')
            );
        }

        $not    = ('DROP' == $action) ? ' ' : ' NOT ';
        $exists = $check ? " IF{$not}EXISTS " : ' ';

        return "{$action} DATABASE{$exists}`{$this->name}`";
    }

    public function __dropDB(string $name, bool $check = false)
    {
        return $this->database('drop', $name, $check);
    }

    public function dropDB(string $name)
    {
        return $this->__dropDB($name);
    }

    public function dropDBIfExists(string $name)
    {
        return $this->__dropDB($name, true);
    }
    
    // @return fluent SQL
    public function __call($name, $params)
    {
        return call_user_func_array([
            (new Table)->ofCreator($this->creator),
            $name
        ],
            $params
        );
    }

    public function ofCreator($creator) : Mysql
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() : SQLSchemaBuilder
    {
        return $this->creator;
    }

    public function exec(string $statement)
    {
        return $this->creator->exec($statement);
    }

    public function fulfillWishFor(SQLSchemaWorker $worker = null)
    {
    }
    
    public function beforeDeath(SQLSchemaWorker $worker = null)
    {
    }
}
