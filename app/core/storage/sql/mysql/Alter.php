<?php

// -------------------------------------------------------------------
//     MySQL table column alter schema builder
//     ses official docs:
//     <https://dev.mysql.com/doc/refman/5.7/en/alter-table.html>
// -------------------------------------------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

use Lif\Core\Storage\SQL\Mysql\ConcreteColumn;

trait Alter
{
    private $alter = null;
    private $first = null;
    private $after = null;
    private $old   = null;

    public function setAlter(string $alter = null) : ConcreteColumn
    {
        $this->alter = $alter;

        return $this;
    }

    public function setOld(string $old = null) : ConcreteColumn
    {
        $this->old = $old;

        return $this;
    }

    public function first() : ConcreteColumn
    {
        $this->first = true;

        return $this;
    }

    public function after(string $column) : ConcreteColumn
    {
        $this->after = $column;

        return $this;
    }
}