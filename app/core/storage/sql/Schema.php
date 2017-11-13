<?php

// -----------------------------------
//     SQL database schema builder
// -----------------------------------

namespace Lif\Core\Storage\SQL;

use Lif\Core\Intf\{SQLSchemaMaster, SQLSchemaWorker};

class Schema implements SQLSchemaMaster
{
    use \Lif\Core\Traits\WithDB;

    private $supported = [
        'mysql',
        'sqlite',
    ];
    private $statements = [];
    private $autocommit = true;

    public function __construct(string $conn = null, string $flush = null)
    {
        $this->setConn($conn);

        $this->flush = $flush;

        $this->db();

        if (! ($this->getDriver())) {
            excp(
                'Missing database driver in current connection: '
                .($this->getConn() ?? '(empty)')
            );
        }

        if (! in_array($this->getDriver(), $this->getSupportedDrivers())) {
            excp(
                'Database schema of driver not supported yet: '
                .($this->getDriver() ?? '(empty)')
            );
        }
    }

    public function createWorker() : SQLSchemaWorker
    {
        if (!($class = nsOf('storage', 'SQL\\'.ucfirst($this->getDriver())))
            || !class_exists($class)
        ) {
            excp('Schema class not exists: '.$class);
        }

        return (new $class)->ofCreator($this);
    }

    public function __call($name, $args)
    {
        $statement = call_user_func_array(
            [$this->createWorker(), $name], $args
        );

        if ($statement) {
            if (is_object($statement)) {
                return $statement;
            }
            if (is_string($statement)) {
                $this->statements[] = $statement;
            }
        }

        return $this;
    }

    public function commitAllOnce()
    {
        if ($statement = implode(";\n", $this->statements)) {
            return $this->exec($statement);
        }
    }

    public function exec(string $statement)
    {
        if ($statement) {
            try {
                return $this->db()->exec($statement);
            } catch (\PDOException $pdoe) {
                excp($pdoe->getMessage()."({$statement})");
            } catch (\Exception $e) {
                excp($e->getMessage()."({$statement})");
            } catch (\Error $e) {
                excp($e->getMessage()."({$statement})");
            }
        }
    }

    public function commit()
    {
        foreach ($this->statements as $statement) {
            return $this->exec($statement);
        }
    }

    public function addSupportDriver(string $driver) : SQLSchemaMaster
    {
        if (! in_array($driver, $this->supported)) {
            $this->supported[] = $driver;
        }

        return $this;
    }

    public function setAutocommit(bool $auto) : SQLSchemaMaster
    {
        $this->autocommit = $auto;

        return $this;
    }

    public function getSupportedDrivers() : array
    {
        return $this->supported;
    }

    public function __destruct()
    {
        if ($this->autocommit) {
            $this->commit();
        }
    }
}
