<?php

// -----------------------------
//     SQLite schema builder
// -----------------------------

namespace Lif\Core\Storage\SQL;

class Sqlite implements \Lif\Core\Intf\SQLSchema
{
    use \Lif\Core\Traits\MethodNotExists;

    public function create(string $table, \Closure $ddl)
    {
    }
}
