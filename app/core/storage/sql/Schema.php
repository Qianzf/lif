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
                if ($statement instanceof \Closure) {
                    $this->exec($statement());

                    return $this;
                }

                return $statement;
            }
            if (is_array($statement)
                && ($query = exists($statement, 'query'))
                && ($callback = exists($statement, 'callback'))
                && ($callback instanceof \Closure)
            ) {
                return $callback($this->query($query));
            }

            if (is_string($statement)) {
                $this->statements[] = $statement;
            }
        }

        return $this;
    }

    private function statementsGetClean()
    {
        $statements = $this->statements;

        $this->statements = [];

        return $statements;
    }

    public function commitAllOnce()
    {
        if ($statement = implode(";\n", $this->statementsGetClean())) {
            return $this->exec($statement);
        }
    }

    public function query(string $statement)
    {
        if ($statement) {
            try {
                return $this->db()->query($statement);
            } catch (\PDOException $pdoe) {
                excp($pdoe->getMessage()."({$statement})");
            } catch (\Exception $e) {
                excp($e->getMessage()."({$statement})");
            } catch (\Error $e) {
                excp($e->getMessage()."({$statement})");
            }
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
        foreach ($this->statementsGetClean() as $statement) {
            $this->exec($statement);
        }

        return true;
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
