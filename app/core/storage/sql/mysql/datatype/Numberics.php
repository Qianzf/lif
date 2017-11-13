<?php

namespace Lif\Core\Storage\SQL\Mysql\DataType;

use Lif\Core\Storage\SQL\Mysql\ConcreteColumn;

trait Numberics
{
    private $decimal   = null;
    private $unsigned  = null;
    private $zerofill  = null;
    
    public function integer(
        string $col = null,
        int $length = 11
    ) : ConcreteColumn
    {
        return $this->number($col, $length, 'integer');
    }

    public function tinyint(
        string $col = null,
        int $length = 4
    ) : ConcreteColumn
    {
        return $this->number($col, $length, 'tinyint');
    }

    public function medint(
        string $col = null,
        int $length = 6
    ) : ConcreteColumn
    {
        return $this->number($col, $length, 'mediumint');
    }

    public function bigint(
        string $col = null,
        int $length = 20
    ) : ConcreteColumn
    {
        return $this->number($col, $length, 'bigint');
    }

    public function int(
        string $col = null,
        int $length = 11
    ) : ConcreteColumn
    {
        return $this->number($col, $length, 'int');
    }

    public function float(
        string $col = null,
        int $integer = 7,
        int $decimal = 2
    ) : ConcreteColumn
    {
        return $this->number($col, $integer, 'float', $decimal);
    }

    public function double(
        string $col = null,
        int $integer = 7,
        int $decimal = 2
    ) : ConcreteColumn
    {
        return $this->number($col, $integer, 'double', $decimal);
    }

    public function real(
        string $col = null,
        int $integer = 7,
        int $decimal = 2
    ) : ConcreteColumn
    {
        return $this->number($col, $integer, 'real', $decimal);
    }

    public function decimal(
        string $col = null,
        int $integer = 7,
        int $decimal = 2
    ) : ConcreteColumn
    {
        return $this->number($col, $integer, 'decimal', $decimal);
    }

    public function numeric(
        string $col = null,
        int $integer = 10,
        int $decimal = 2
    ) : ConcreteColumn
    {
        return $this->number($col, $integer, 'numeric', $decimal);
    }

    public function number(
        string $col = null,
        int $length = 11,
        string $type = 'int',
        int $decimal = null
    ) : ConcreteColumn
    {
        if ($col) {
            $this->conflict('name', $col);
            $this->name = $col;
        }

        $this->conflict('type', $type);
        $this->type   = $type;
        $this->length = $length;

        if (! is_null($decimal)) {
            $this->decimal = $decimal;
        }

        return $this;
    }

    private function grammarOfNumberic(string $length) : string
    {
        $grammer  = $length;
        $grammer .= $this->unsigned ? 'UNSIGNED ' : '';
        $grammer .= $this->zerofill ? 'ZEROFILL ' : '';

        return $this->fillGrammer($grammer);
    }

    private function grammarOfFloat() : string
    {
        return $this->grammarOfNumberic($this->getFloatLength());
    }

    private function grammarOfInteger() : string
    {
        return $this->grammarOfNumberic($this->getIntegerLength());
    }

    private function getIntegerLength()
    {
        $this->decimal = $this->decimal ?? 2;
        
        return $this->length ? "({$this->length}) " : '';
    }

    private function getFloatLength()
    {
        $this->decimal = $this->decimal ?? 2;
        
        return $this->length ? "({$this->length},{$this->decimal}) " : '';
    }
}
