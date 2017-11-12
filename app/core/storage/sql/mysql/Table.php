<?php

// ----------------------------------
//     MySQL table schema builder
// ----------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

class Table
{
    use \Lif\Core\Traits\MethodNotExists;

    private $name      = null;
    private $comment   = null;
    private $column    = null;
    private $autoincre = null;
    private $engine    = 'InnoDB';
    private $charset   = 'utf8';
    private $collation = 'utf8_unicode_ci';
    private $indexes   = [];
    private $temporary = false;

    public function createIfNotExists(
        string $table,
        \Closure $ddl,
        bool $temporary = false
    ) {
        return $this->create(
            $table,
            $ddl,
            $temporary,
            true
        );
    }

    private function definitions(
        string $table,
        \Closure $ddl
    ) : string
    {
        if (! ($this->name = $table)) {
            excp(
                'Missing or illegal table name to create: '
                .$table
            );
        }

        call_user_func(
            $ddl,
            ($this->column = (new AbstractColumn)->ofTable($this))
        );

        if (! ($columns = $this->column->getConcretes())) {
            excp('Missing table definition.');
        }

        $definitions = '';
        foreach ($columns as $column) {
            $definitions .= $column->grammar();

            if (false !== next($columns)) {
                $definitions .= ',';
            }

            $definitions .= "\n";
        }

        return $definitions ?: '';
    }

    public function create(
        string $table,
        \Closure $ddl,
        bool $temporary = false,
        bool $nonExists = false
    ) : string
    {
        $definitions = $this->definitions($table, $ddl);
        $temporary   = ($this->temporary = $temporary)
        ? ' TEMPORARY ' : ' ';
        $nonExists   = $nonExists ? ' IF NOT EXISTS ' : ' ';

        $schema  = "CREATE{$temporary}TABLE{$nonExists}`{$this->name}` (\n";
        $schema .= $definitions;
        $schema .= ') ';
        $schema .= "ENGINE={$this->engine} ";
        $schema .= $this->getAutoincre();
        $schema .= "DEFAULT CHARACTER SET {$this->charset} ";
        $schema .= "COLLATE {$this->collation} ";
        $schema .= $this->getComment();

        return $schema;
    }

    public function alter(
        string $table,
        \Closure $ddl
    )
    {
        $schema = '';
        
        return $schema;
    }

    public function dropIfExists(...$tables)
    {
        if ($tables) {
            $tables = implode(',', $tables);

            return "DROP TABLE IF EXISTS {$tables}";
        }
    }

    public function drop(...$tables)
    {
        if ($tables) {
            $tables = implode(',', $tables);

            return "DROP TABLE {$tables}";
        }
    }

    public function collate(...$params)
    {
        
    }

    public function charset(...$params)
    {

    }

    public function engine(...$params)
    {
        if (1 === ($cnt = count($params))) {
            $this->engine = $params[0] ?? null;

            return $this;
        }

        if (2 === $cnt) {
            if (! ($this->name = ($params[0] ?? null))) {
                excp('Missing table name when setting engine.');
            }
            $this->engine = $params[1] ?? null;

            return
            "ALTER TABLE `{$this->name}` ENGINE = {$this->engine}";
        }
    }

    public function autoincre(...$params)
    {
        if (1 === ($cnt = count($params))) {
            $this->autoincre = $params[0] ?? null;

            return $this;
        }

        if (2 === $cnt) {
            if (! ($this->name = ($params[0] ?? null))) {
                excp('Missing table name when setting auto_increment.');
            }
            $this->autoincre = intval($params[1] ?? null);

            return
            "ALTER TABLE `{$this->name}` AUTO_INCREMENT = {$this->autoincre}";
        }
    }

    public function getAutoincre() : string
    {
        return $this->autoincre
        ? "AUTO_INCREMENT={$this->autoincre}"
        : '';
    }

    public function getComment() : string
    {
        return $this->comment
        ? ('COMMENT='.(ldo()->quote($this->comment)).' ')
        : '';
    }

    public function comment(...$params)
    {
        if (1 === ($cnt = count($params))) {
            $this->comment = $params[0] ?? null;

            return $this;
        }

        if (2 === $cnt) {
            if (! ($this->name = ($params[0] ?? null))) {
                excp('Missing table name when comment on it.');
            }
            $this->comment = $params[1] ?? null;
            $comment = ldo()->quote($this->comment);

            return
            "ALTER TABLE `{$this->name}` COMMENT {$comment}";
        }
    }
}
