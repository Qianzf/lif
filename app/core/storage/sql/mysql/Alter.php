<?php

namespace Lif\Core\Storage\SQL\Mysql;

use Lif\Core\Storage\SQL\Mysql\ConcreteColumn;

trait Alter
{
    private $alter = null;
    private $first = null;
    private $after = null;

    public function setAlter(string $alter = null) : ConcreteColumn
    {
        $this->alter = $alter;

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