<?php

// -----------------------------
//     SQLite schema builder
// -----------------------------

namespace Lif\Core\Storage\SQL;

use Lif\Core\Intf\{SQLSchemaWorker, SQLSchemaBuilder};

class Sqlite implements SQLSchemaWorker
{
    private $creator = null;
    private $name    = null;
    
    use \Lif\Core\Traits\MethodNotExists;

    public function create(string $table, \Closure $ddl)
    {
    }

    public function ofCreator(SQLSchemaBuilder $creator) : SQLSchemaBuilder
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() : SQLSchemaBuilder
    {
        return $this->creator;
    }

    public function fulfillWishFor(SQLSchemaWorker $worker = null)
    {
    }
    
    public function beforeDeath(SQLSchemaWorker $worker = null)
    {
    }
}
