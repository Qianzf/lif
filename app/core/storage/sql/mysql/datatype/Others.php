<?php

namespace Lif\Core\Storage\SQL\Mysql\DataType;

use Lif\Core\Storage\SQL\Mysql\ConcreteColumn;

trait Others
{
    private $enum = null;
    private $set  = null;

    public function enum(
        string $col,
        ...$enum
    ) : ConcreteColumn
    {
        if (! $col) {
            excp('Missing enum column name');
        }
        if (! $enum) {
            excp('Missing enum values');
        }

        $this->conflict('name', $col);
        $this->conflict('type', 'ENUM');
        $this->enum = $enum;

        return $this;
    }

    public function set(
        string $col,
        ...$set
    ) : ConcreteColumn
    {
        if (! $col) {
            excp('Missing set column name');
        }
        if (! $set) {
            excp('Missing set values');
        }

        $this->conflict('name', $col);
        $this->conflict('type', 'SET');
        $this->set = $set;

        return $this;
    }

    public function json(string $col = null) : ConcreteColumn
    {
        if ($col) {
            $this->conflict('name', $col);
        }

        $this->conflict('type', 'JSON');

        return $this;
    }

    private function grammarOfEnum() : string
    {
        return $this->fillGrammer($this->getEnum());
    }

    private function getEnum()
    {
        return $this->genStringable($this->enum);
    }

    private function genStringable(array $data)
    {
        if (! $data) {
            return '';
        }

        array_walk($data, function (&$item) {
            $item = ldo()->quote($item);
        });

        $data = implode(',', $data);

        return "({$data})";
    }

    private function grammarOfSet() : string
    {
        return $this->fillGrammer($this->getSet());
    }

    private function getSet()
    {
        return $this->genStringable($this->set);
    }

    private function grammarOfJson() : string
    {
        return $this->fillGrammer();
    }
}
