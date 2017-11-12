<?php

namespace Lif\Core\Storage\SQL\Mysql\DataType;

use Lif\Core\Storage\SQL\Mysql\ConcreteColumn;

trait Strings
{
    public function tinytext(
        string $col = null,
        int $length = 255
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'tinytext');
    }

    public function mediumtext(
        string $col = null,
        int $length = 255
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'mediumtext');
    }

    public function medtext(
        string $col = null,
        int $length = 255
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'mediumtext');
    }

    public function longtext(
        string $col = null,
        int $length = 255
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'longtext');
    }

    public function varchar(
        string $col = null,
        int $length = 255
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'varchar');
    }

    public function string(
        string $col = null,
        int $length = 255
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'varchar');
    }

    public function char(
        string $col = null,
        int $length = 32
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'char');
    }

    public function bin(
        string $col = null,
        int $length = 1024
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'binary');
    }

    public function binary(
        string $col = null,
        int $length = 1024
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'binary');
    }

    public function varbin(
        string $col = null,
        int $length = 1024
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'varbinary');
    }

    public function varbinary(
        string $col = null,
        int $length = 1024
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'varbinary');
    }

    public function tinyblob(string $col = null) : ConcreteColumn
    {
        return $this->strings($col, null, 'tinyblob');
    }

    public function medblob(string $col = null) : ConcreteColumn
    {
        return $this->strings($col, null, 'mediumblob');
    }

    public function mediumblob(string $col = null) : ConcreteColumn
    {
        return $this->strings($col, null, 'mediumblob');
    }

    public function longblob(string $col = null) : ConcreteColumn
    {
        return $this->strings($col, null, 'longblob');
    }

    public function blob(
        string $col = null,
        int $length = 1024
    ) : ConcreteColumn
    {
        return $this->strings($col, $length, 'blob');
    }

    private function strings(
        string $col = null,
        int $length = null,
        string $type = 'string'
    ) : ConcreteColumn
    {
        if ($col) {
            if (($col == intval($col)) && ($col > 0)) {
                $this->length = $col;
                // dd($this->length);
            } else {
                $this->conflict('name', $col);
                $this->name = $col;
            }
        }

        $this->conflict('type', $type);
        $this->type   = $type;

        if (!is_null($length) && !$this->length) {
            $this->length = $length;
        }

        return $this;
    }

    private function grammarOfBinary() : string
    {
        return $this->fillGrammer($this->getStringLength());
    }

    private function grammarOfString() : string
    {
        return $this->fillGrammer($this->getStringLength());
    }

    private function getStringLength()
    {
        return $this->length
        ? "({$this->length}) "
        : '';
    }
}
