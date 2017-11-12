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
        return $this->__drop($tables, true);
    }

    public function drop(...$tables)
    {
        return $this->__drop($tables);
    }

    private function __drop(
        array $tables = null,
        bool $exists = null
    ) : string
    {
        if ($tables) {
            array_walk($tables, function (&$item) {
                $item = "`$item`";
            });

            $tables = implode(',', $tables);

            unset($item);

            $ifexists = $exists ? 'IF EXISTS' : '';

            return "DROP TABLE {$ifexists} {$tables}";
        }
    }

    public function collate(...$params)
    {
        return $this->set(
            'collate',
            $params,
            function (array $params) : string
            {
                return $this->collate = $params[1] ?? 'utf8_unicode_ci';
            }
        );   
    }

    public function charset(...$params)
    {
        return $this->set(
            'default charset',
            $params,
            function (array $params) : string
            {
                return $this->charset = $params[1] ?? 'utf8mb4';
            }
        );
    }

    public function engine(...$params)
    {
        return $this->set(
            'engine',
            $params,
            function (array $params) : string
            {
                return $this->engine = $params[1] ?? 'InnoDB';
            }
        );
    }

    public function autoincre(...$params)
    {
        return $this->set(
            'auto_increment',
            $params,
            function (array $params) : string
            {
                return $this->autoincre = intval($params[1] ?? null);
            }
        );
    }

    public function comment(...$params)
    {
        return $this->set(
            'comment',
            $params,
            function (array $params) : string
            {
                $this->comment = $params[1] ?? null;

                return ldo()->quote($this->comment);
            },
            'comment on it'
        );
    }

    public function set(
        string $attr,
        array $params,
        \Closure $calback,
        string $desc = null
    ) : string
    {
        if (1 === ($cnt = count($params))) {
            $this->$attr = $params[0] ?? null;

            return $this;
        }

        if (2 === $cnt) {
            $attr = strtoupper($attr);
            if (! ($this->name = ($params[0] ?? null))) {
                excp(
                    'Missing table name when '
                    .($desc ?? "setting {$attr}")
                );
            }

            $value = $calback($params);

            return "ALTER TABLE `{$this->name}` {$attr}={$value}";
        }
    }

    private function getAutoincre() : string
    {
        return $this->autoincre
        ? "AUTO_INCREMENT={$this->autoincre}"
        : '';
    }

    private function getComment() : string
    {
        return $this->comment
        ? ('COMMENT='.(ldo()->quote($this->comment)))
        : '';
    }
}
