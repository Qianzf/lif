<?php

// ----------------------------
//     MySQL schema builder
// ----------------------------

namespace Lif\Core\Storage\SQL;

class Mysql
{
    use \Lif\Core\Traits\MethodNotExists;

    private $sql = [];

    public function create(string $table, \Closure $ddl)
    {
        call_user_func($ddl, $this);
    }

    public function col(string $col) : Mysql
    {
        return $this;
    }

    public function int(string $col = null) : Mysql
    {
        return $this;
    }

    public function pk(string $col = null) : Mysql
    {
        return $this;
    }

    public function autoi(string $col = null) : Mysql
    {
        return $this;
    }
}
