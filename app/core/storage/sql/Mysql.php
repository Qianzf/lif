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
