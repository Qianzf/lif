<?php

// ----------------------------------
//     MySQL table schema builder
// ----------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

class Table
{
    private $name      = null;
    private $comment   = null;
    private $column    = null;
    private $autoincre = null;
    private $engine    = 'InnoDB';
    private $charset   = 'utf8';
    private $collation = 'utf8_unicode_ci';
    private $indexes   = [];
    private $temporary = false;
    private $alter     = false;

    public function table(string $name) : Table
    {
        $this->name  = $name;
        $this->alter = true;

        return $this;
    }

    private function genGrammers() : string
    {
        if (! ($columns = $this->column->getConcretes())) {
            excp('Missing table definition.');
        }

        $grammers = '';
        foreach ($columns as $column) {
            $grammers .= $column->grammar();

            if (false !== next($columns)) {
                $grammers .= ',';
            }

            $grammers .= "\n";
        }

        return trim($grammers ?: '');
    }

    private function createColumn() : AbstractColumn
    {
        if (!$this->column || !($this->column instanceof AbstractColumn)) {
            $this->column = (new AbstractColumn)->ofTable($this);
        }

        return $this->column;
    }

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
                'Missing or illegal table name to definition: '
                .($this->name ?? '(empty)')
            );
        }

        call_user_func($ddl, $this->createColumn());

        return $this->genGrammers();
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

    public function alter(string $table, \Closure $ddl)
    {
        return $this->genAlterSchema($this->definitions($table, $ddl));
    }

    public function dropIfExists(...$tables)
    {
        return $this->__drop($tables, true);
    }

    public function drop(...$tables)
    {
        return $this->__drop($tables);
    }

    private function alterations($alteration, ...$params)
    {
        if (! ($this->name)) {
            excp(
                'Missing or illegal table name to alteration: '
                .($this->name ?? '(empty)')
            );
        }

        $result = call_user_func_array([
            $this->createColumn(),
            $alteration
        ], $params);

        if ($result instanceof AbstractColumn) {
            $this->column = $result;

            return $this;
        }

        return $this->genGrammers();
    }

    private function genAlterSchema(string $alteration) : string
    {
        return "ALTER TABLE `{$this->name}` {$alteration}";
    }

    public function addCol(string $name)
    {
        return $this->addColumn($name);
    }

    public function addColumn(string $name)
    {
        return $this->alterations('add', $name);
    }

    public function dropCol(string $name)
    {
        return $this->dropColumn($name);
    }

    public function dropColumn(string $name)
    {
        return $this->genAlterSchema($this->alterations('drop', $name));
    }

    public function dropColDefault(string $column)
    {
        return $this->dropColumnDefault($column);
    }

    public function dropColumnDefault(string $column)
    {
        return $this->genAlterSchema(
            $this->alterations('dropDefault', $column)
        );
    }

    public function setColDefault(string $column, $default)
    {
        return $this->setColumnDefault($column, $default);
    }

    public function setColumnDefault(string $column, $default)
    {
        return $this->genAlterSchema(
            $this->alterations('setDefault', $column, $default)
        );
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

    public function rename(string $name, string $rename = 'TO')
    {
        $rename = strtoupper($rename);

        return "ALTER TABLE `{$this->name}` RENAME {$rename} `{$name}`";
    }

    public function renameAs(string $name)
    {
        return $this->rename($name, 'AS');
    }

    public function renameTo(string $name)
    {
        return $this->rename($name);
    }

    public function __call($name, $args)
    {
        call_user_func_array([$this->createColumn(), $name], $args);

        return $this;
    }

    public function collate(...$params)
    {
        return $this->set(
            'collate',
            $params,
            function ($value) : string
            {
                return $this->collate = $value ?? 'utf8_unicode_ci';
            }
        );   
    }

    public function charset(...$params)
    {
        return $this->set(
            'default charset',
            $params,
            function ($value = null) : string
            {
                return $this->charset = $value ?? 'utf8mb4';
            }
        );
    }

    public function engine(...$params)
    {
        return $this->set(
            'engine',
            $params,
            function ($value = null) : string
            {
                return $this->engine = $value ?? 'InnoDB';
            }
        );
    }

    public function autoincre(...$params)
    {
        return $this->set(
            'auto_increment',
            $params,
            function ($value = null) : string
            {
                return $this->autoincre = intval($value);
            }
        );
    }

    public function comment(...$params)
    {
        return $this->set(
            'comment',
            $params,
            function ($value) : string
            {
                return ldo()->quote($this->comment = $value);
            },
            'comment on it'
        );
    }

    public function set(
        string $attr,
        array $params,
        \Closure $calback,
        string $desc = null
    ) {
        $attr = strtoupper($attr);

        if (1 === ($cnt = count($params))) {
            $value = $params[0] ?? null;
            if ($this->alter) {
                if ($value = $calback($value)) {
                    return "ALTER TABLE `{$this->name}` {$attr}={$value}";
                }
            }

            $this->$attr = $value;

            return $this;
        }

        if (2 === $cnt) {
            if (! ($this->name = ($params[0] ?? null))) {
                excp(
                    'Missing table name when '
                    .($desc ?? "setting {$attr}")
                );
            }

            if ($value = $calback($params[1] ?? null)) {
                return "ALTER TABLE `{$this->name}` {$attr}={$value}";
            }
        }
    }

    public function getName()
    {
        return $this->name;
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
