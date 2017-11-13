<?php

// -------------------------------------------------------------------
//     MySQL table column schema builder
//     ses official docs:
//     <https://dev.mysql.com/doc/refman/5.7/en/create-table.html>
// -------------------------------------------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

use Lif\Core\Intf\{SQLSchemaWorker, SQLSchemaBuilder};

class ConcreteColumn implements SQLSchemaWorker
{
    use \Lif\Core\Traits\MethodNotExists;
    use Grammers;
    use Alter;
    use DataType\Numberics;
    use DataType\Strings;
    use DataType\Times;
    use DataType\Others;

    private $creator   = null;
    private $name      = null;
    private $type      = null;
    private $length    = null;
    private $nullable  = null;
    private $increable = null;
    private $default   = null;
    private $unique    = null;
    private $primary   = null;
    private $comment   = null;
    private $format    = null;
    private $storage   = null;

    public function ofCreator(AbstractColumn $creator) : ConcreteColumn
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() : SQLSchemaBuilder
    {
        return $this->creator;
    }

    public function setName(string $name = null) : ConcreteColumn
    {
        $this->name = $name;

        return $this;
    }

    public function grammar()
    {
        if (! $this->name) {
            excp(
                'Missing or illegal column name: '
                .($this->name ?? '(empty)')
            );
        }

        if (! $this->type) {
            excp(
                'Missing or illegal column type: '
                .($this->type ?? '(empty)')
            );
        }

        $this->type = strtoupper($this->type);

        foreach ($this->grammers as $grammer => $group) {
            if (in_array($this->type, $group)) {
                $grammerGroupHandler = 'grammarOf'.ucfirst($grammer);

                if (! method_exists($this, $grammerGroupHandler)) {
                    excp(
                        'Grammer group handler not exists: '
                        .$grammerGroupHandler
                    );
                }

                return call_user_func([$this, $grammerGroupHandler]);
            }
        }
    }

    private function fillGrammer(string $part = null) : string
    {
        $grammer  = $this->commonPrefix();
        $grammer .= $part ?? '';
        $grammer .= $this->commonSuffix();

        return $grammer;
    }

    public function commonSuffix() : string
    {
        $suffix  = $this->nullable  ? '' : ' NOT NULL ';
        $suffix .= empty_safe($this->default)
        ? ''
        : " Default {$this->default} ";
        $suffix .= $this->increable ? ' AUTO_INCREMENT ' : '';
        $suffix .= $this->unique    ? ' UNIQUE KEY ' : '';
        $suffix .= $this->primary   ? ' PRIMARY KEY ' : '';
        $suffix .= $this->getComment();
        $suffix .= $this->getFormat();
        $suffix .= $this->getStorage();

        if ($this->alter) {
            $suffix .= (
                $this->first ? ' FIRST ' : ''
            ) ?: (
                $this->after ? " AFTER `{$this->after}` " : ''
            );
        }

        return $suffix;
    }

    public function commonPrefix()
    {
        $prefix  = $this->alter
        ? strtoupper($this->alter).' '
        : '';

        $prefix .= $this->old ? "`{$this->old}` " : '';
        $prefix .= "`{$this->name}` ";
        $prefix .= "{$this->type}";

        return $prefix;
    }

    private function getComment()
    {
        return $this->comment
        ? ('COMMENT '.(ldo()->quote($this->comment)))
        : '';
    }

    private function getFormat()
    {
        return $this->format
        ? ('COLUMN_FORMAT '.(ldo()->quote($this->format)))
        : '';
    }

    private function getStorage()
    {
        return $this->storage
        ? ('STORAGE '.(ldo()->quote($this->storage)))
        : '';
    }

    public function __get(string $attr)
    {
        return $this->$attr ?? null;
    }

    public function format(string $format) : ConcreteColumn
    {
        $_format = strtoupper($format);
        if (! in_array($_format, $this->formats)) {
            excp('Illegal column format: '.$format);
        }

        $this->format = $_format;

        return $this;
    }

    public function storage(string $storage) : ConcreteColumn
    {
        $_storage = strtoupper($storage);
        if (! in_array($_storage, $this->storages)) {
            excp('Illegal column storage type: '.$storage);
        }

        $this->storage = $_storage;

        return $this;
    }

    public function col(string $name) : ConcreteColumn
    {
        $this->conflict('name', $name);

        $this->name = $name;

        return $this;
    }

    public function pk(
        string $name = null,
        string $type = 'int',
        int $length = 11
    ) : ConcreteColumn
    {
        if ($name) {
            $this->conflict('name', $name);
            $this->name = $name;
        }
        if ($type) {
            $this->conflict('type', $type);
            $this->type = $type;
        }

        $this->length    = $length;
        $this->primary   = true;
        $this->increable = true;

        return $this;
    }

    public function comment(string $comment) : ConcreteColumn
    {
        $this->comment = $comment;

        return $this;
    }

    public function notnull(string $name = null) : ConcreteColumn
    {
        $this->nullable = false;
        
        if (! is_null($name)) {
            $this->conflict('name', $name);
            $this->name = $name;
        }

        return $this;
    }

    public function default($default = null) : ConcreteColumn
    {
        $this->default = $default;

        return $this;
    }

    public function nullable(string $name = null) : ConcreteColumn
    {
        $this->nullable = true;

        if (! is_null($name)) {
            $this->conflict('name', $name);
            $this->name = $name;
        }

        return $this;
    }

    public function unique(string $name = null) : ConcreteColumn
    {
        $this->unique = true;

        if (! is_null($name)) {
            $this->conflict('name', $name);
            $this->name = $name;
        }

        return $this;
    }

    public function increable(string $name = null) : ConcreteColumn
    {
        if (is_null($name)) {
            $this->conflict('name', $name);
        }

        $this->increable = true;

        return $this;
    }

    private function conflict(
        string $attr,
        string $conflict
    ) : ConcreteColumn
    {
        $name = $this->name ? " `{$this->name}` " : ' ';

        if ($this->$attr ?? false) {
            excp(
                "Column{$name}already has {$attr} `{$this->$attr}`, and trying re-{$attr} it with `{$conflict}`"
            );
        }

        return $this;
    }

    public function beforeDeath(SQLSchemaWorker $worker = null)
    {
        return $this->creator->beforeDeath($this);
    }

    public function __destruct()
    {
        return $this->beforeDeath($this);
    }
}
