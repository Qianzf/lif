<?php

// ----------------------------------
//     MySQL table schema builder
// ----------------------------------

namespace Lif\Core\Storage\SQL\Mysql;

use \Lif\Core\Intf\{SQLSchemaMaster, SQLSchemaWorker, SQLSchemaBuilder};

class Table implements SQLSchemaWorker
{
    private $creator   = null;
    private $name      = null;
    private $comment   = null;
    private $column    = null;
    private $autoincre = null;
    private $engine    = 'InnoDB';
    private $charset   = 'utf8';
    private $collation = 'utf8_unicode_ci';
    private $indexes   = [];
    private $temporary = false;
    private $autonomy  = false;    // Weather alterations handled by self

    public function ofCreator(SQLSchemaMaster $creator) : Table
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() : SQLSchemaBuilder
    {
        return $this->creator;
    }

    public function table(string $name) : Table
    {
        $this->name     = $name;
        $this->autonomy = true;

        return $this;
    }

    private function createColumn() : AbstractColumn
    {
        return $this->column = (new AbstractColumn)->ofCreator($this);
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

    public function dropTableIfExists(...$tables)
    {
        return $this->__drop(true, $tables);
    }

    public function dropIfExists(...$tables)
    {
        return $this->__drop(true, $tables);
    }

    public function dropTable(...$tables)
    {
        return $this->__drop(false, $tables);
    }

    public function drop(...$tables)
    {
        return $this->__drop(false, $tables);
    }

    private function __drop(
        bool $exists = false,
        array $tables = null
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

    private function autonomy($statement)
    {
        if ($this->autonomy) {
            if (is_string($statement)) {
                $_statement = $statement;
            } elseif (is_object($statement)
                && method_exists($statement, 'grammar')
            ) {
                $_statement = $this->genAlterSchema($statement->grammar());
            }

            return $this->creator->exec($_statement);
        }

        return $statement;
    }

    // Generate grammers group into one statement
    // via column bound to current table
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

    // Processing column groups definition
    private function definitions(string $table, \Closure $ddl) : string
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

    // Processing single column alteration
    private function alterations(string $alteration, ...$params)
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

        if (is_object($result)) {
            return $result;
        }

        return $this->genGrammers();
    }

    private function genAlterSchema($alteration)
    {
        if (is_string($alteration) && $alteration) {
            return "ALTER TABLE `{$this->name}` {$alteration}";
        }

        return $alteration;
    }

    public function addCol(string $name)
    {
        return $this->addColumn($name);
    }

    public function addColumn(string $name)
    {
        return $this->alterations('add', $name);
    }

    public function modifyCol(string $name)
    {
        return $this->modifyColumn($name);
    }

    public function modifyColumn(string $name)
    {
        return $this->alterations('modify', $name);
    }

    public function changeCol(string $old, string $new)
    {
        return $this->changeColumn($old, $new);
    }

    public function changeColumn(string $old, string $new)
    {
        return $this->alterations('change', $old, $new);
    }

    public function dropCol(string $name)
    {
        return $this->dropColumn($name);
    }

    public function dropColumn(string $name)
    {
        return $this->__alter('drop', $name);
    }

    public function dropColDefault(string $column)
    {
        return $this->dropColumnDefault($column);
    }

    public function dropColumnDefault(string $column)
    {
        return $this->__alter('dropDefault', $column);
    }

    public function setColDefault(string $column, $default)
    {
        return $this->setColumnDefault($column, $default);
    }

    public function setColumnDefault(string $column, $default)
    {
        return $this->__alter('setDefault', $column, $default);
    }

    private function __alter(string $alteration, ...$params)
    {
        return $this->autonomy($this->genAlterSchema(
            $this->alterations($alteration, ...$params)
        ));
    }

    public function rename(string $name, string $rename = 'TO')
    {
        $rename = strtoupper($rename);

        return $this->autonomy(
            "ALTER TABLE `{$this->name}` RENAME {$rename} `{$name}`"
        );
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
            function ($value) : string {
                return $this->collate = $value ?? 'utf8_unicode_ci';
            }
        );   
    }

    public function charset(...$params)
    {
        return $this->set(
            'default charset',
            $params,
            function ($value = null) : string {
                return $this->charset = $value ?? 'utf8mb4';
            }
        );
    }

    public function engine(...$params)
    {
        return $this->set(
            'engine',
            $params,
            function ($value = null) : string {
                return $this->engine = $value ?? 'InnoDB';
            }
        );
    }

    public function autoincre(...$params)
    {
        return $this->set(
            'auto_increment',
            $params,
            function ($value = null) : string {
                return $this->autoincre = intval($value);
            }
        );
    }

    public function comment(...$params)
    {
        return $this->set(
            'comment',
            $params,
            function ($value) : string {
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
            if ($value = $calback($value)) {
                return $this->autonomy(
                    "ALTER TABLE `{$this->name}` {$attr}={$value}"
                );
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
        return $this->autoincre ? "AUTO_INCREMENT={$this->autoincre} " : '';
    }

    private function getComment() : string
    {
        return $this->comment ? ('COMMENT='.(ldo()->quote($this->comment))) : '';
    }

    public function fulfillWishFor(SQLSchemaWorker $worker = null)
    {
        return $this->autonomy($this->genAlterSchema($this->genGrammers()));
    }

    public function beforeDeath(SQLSchemaWorker $worker = null)
    {
    }
}
