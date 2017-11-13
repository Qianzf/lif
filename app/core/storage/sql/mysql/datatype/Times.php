<?php

namespace Lif\Core\Storage\SQL\Mysql\DataType;

use Lif\Core\Storage\SQL\Mysql\ConcreteColumn;

trait Times
{
    public function date(string $col = null) : ConcreteColumn
    {
        return $this->times('date', $col, null);
    }

    public function year(string $col = null) : ConcreteColumn
    {
        return $this->times('year', $col, null);
    }

    public function time(
        string $col = null,
        string $fsp = null
    ) : ConcreteColumn
    {
        return $this->times('time', $col, $fsp);
    }

    public function timestamp(
        string $col = null,
        string $fsp = null
    ) : ConcreteColumn
    {
        return $this->times('timestamp', $col, $fsp);
    }

    public function datetime(
        string $col = null,
        string $fsp = null
    ) : ConcreteColumn
    {
        return $this->times('datetime', $col, $fsp);
    }


    public function times(
        string $type,
        string $col = null,
        string $fsp = null
    ) : ConcreteColumn
    {
        if ($col) {
            $this->conflict('name', $col);
        }

        $this->conflict('type', $type);

        return $this;
    }
}
