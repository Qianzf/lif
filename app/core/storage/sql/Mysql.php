<?php

// ----------------------------
//     MySQL schema builder
// ----------------------------

namespace Lif\Core\Storage\SQL;

use Lif\Core\Storage\SQL\Mysql\Table;
use Lif\Core\Intf\{SQLSchemaWorker, SQLSchemaBuilder};

class Mysql implements SQLSchemaWorker
{
    private $creator = null;
    private $name    = null;
    private $charset = 'utf8mb4';
    private $collate = 'utf8mb4_unicode_ci';

    public function hasTable(string $name)
    {
        $query  = 'SELECT `table_name` AS `exists` ';
        $query .= 'FROM `information_schema`.`tables` ';
        $query .= "WHERE `table_schema`=DATABASE() AND `table_name`='{$name}'";
        $callback = function (\PDOStatement $statement) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return !empty_safe($result['exists']);
        };

        return [
            'query'    => $query,
            // 'query'    => "SHOW TABLES LIKE '{$name}'",
            'callback' => $callback,
        ];
    }

    public function hasDBUsed()
    {
        $callback = function (\PDOStatement $statement) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result['status'] ?? false;
        };

        return [
            'query'    => 'SELECT DATABASE() AS `status` FROM DUAL',
            'callback' => $callback,
        ];
    }

    public function hasDB(string $name)
    {
        $callback = function (\PDOStatement $statement) {
            return !empty_safe($statement->fetch(\PDO::FETCH_ASSOC));
        };

        return [
            'query'    => "SHOW DATABASES LIKE '{$name}'",
            'callback' => $callback,
        ];
    }

    public function useDB(string $db = null)
    {
        return function () use ($db) {
            return "USE `{$db}`";
        };
    }

    public function createDBIfNotExists(
        string $name,
        \Closure $callable = null
    )
    {
        return $this->__createDB($name, $callable, true);
    }

    public function createDB(
        string $name,
        \Closure $callable = null,
        bool $check = false
    )
    {
        return $this->__createDB($name, $callable, $check);
    }

    private function __createDB(
        string $name,
        \Closure $callable = null,
        bool $check = false
    )
    {
        if ($callable) {
            call_user_func($callable, $this);    // setting db attrs only
        }

        $schema  = $this->database('create', $name, $check);
        $schema .= $this->getCharsetGrammer();
        $schema .= $this->getCollateGrammer();

        return $schema;
    }

    private function getCharsetGrammer()
    {
        return $this->charset
        ? " DEFAULT CHARACTER SET = {$this->charset} " : '';
    }

    private function getCollateGrammer()
    {
        return $this->collate
        ? " DEFAULT COLLATE = {$this->collate} " : '';
    }

    public function collate(string $collate)
    {
        $this->collate = $collate;

        return $this;
    }

    public function charset(string $charset)
    {
        $this->charset = $charset;

        return $this;
    }

    private function database(
        string $action,
        string $name,
        bool $check = false
    )
    {
        if (! ($action = strtoupper($action))) {
            excp(
                'Missing or illegal database manipulation: '
                .($this->name ?? '(empty)')
            );
        }
        if (! ($this->name = $name)) {
            excp(
                'Missing or illegal database name to create: '
                .($this->name ?? '(empty)')
            );
        }

        $not    = ('DROP' == $action) ? '' : 'NOT';
        $exists = $check ? "IF {$not} EXISTS" : '';

        return "{$action} DATABASE {$exists} `{$this->name}`";
    }

    public function __dropDB(string $name, bool $check = false)
    {
        return $this->database('drop', $name, $check);
    }

    public function dropDB(string $name)
    {
        return $this->__dropDB($name);
    }

    public function dropDBIfExists(string $name)
    {
        return $this->__dropDB($name, true);
    }
    
    // @return fluent SQL
    public function __call($name, $params)
    {
        return call_user_func_array([
            (new Table)->ofCreator($this->creator),
            $name
        ],
            $params
        );
    }

    public function ofCreator(SQLSchemaBuilder $creator) : SQLSchemaBuilder
    {
        $this->creator = $creator;

        return $this;
    }

    public function getCreator() : SQLSchemaBuilder
    {
        return $this->creator;
    }

    public function query(string $statement)
    {
        return $this->creator->query($statement);
    }

    public function exec(string $statement)
    {
        return $this->creator->exec($statement);
    }

    public function fulfillWishFor(SQLSchemaWorker $worker = null)
    {
    }
    
    public function beforeDeath(SQLSchemaWorker $worker = null)
    {
    }
}
