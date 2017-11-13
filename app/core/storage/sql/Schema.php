<?php

// -----------------------------------
//     SQL database schema builder
// -----------------------------------

namespace Lif\Core\Storage\SQL;

class Schema implements \Lif\Core\Intf\DBConn
{
    use \Lif\Core\Traits\WithDB;

    private $supported = [
        'mysql',
        'sqlite',
    ];
    private $statements = [];
    private $autocommit = true;
    private $table      = null;    // Table schema object

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

    private function createDriver()
    {
        if ($this->table) {
            return $this->table;
        }

        if (!($class = nsOf('storage', 'SQL\\'.ucfirst($this->getDriver())))
            || !class_exists($class)
        ) {
            excp('Schema class not exists: '.$class);
        }

        return (new $class);
    }

    public function __call($name, $args)
    {
        $statement = call_user_func_array(
            [$this->createDriver(), $name], $args
        );

        if ($statement) {
            if (is_object($statement)) {
                $this->table = $statement;
                
                return $this;
            }

            $this->statements[] = $statement;
        }
    }

    public function commitAllOnce()
    {
        try {
            if ($this->statements) {
                $this->db()->exec(
                    implode(";\n", $this->statements)
                );
            }
        } catch (\PDOException $pdoe) {
            excp($pdoe->getMessage()."({$statement})");
        } catch (\Error $e) {
            excp($e->getMessage()."({$statement})");
        }
    }

    public function commit()
    {
        foreach ($this->statements as $statement) {
            // dd($statement);
            try {
                $this->db()->exec($statement);
            } catch (\PDOException $pdoe) {
                excp($pdoe->getMessage()."({$statement})");
            } catch (\Error $e) {
                excp($e->getMessage()."({$statement})");
            }
        }
    }

    private function addSupportDriver(string $driver) : Schema
    {
        if (! in_array($driver, $this->supported)) {
            $this->supported[] = $driver;
        }

        return $this;
    }

    public function setAutocommit(bool $auto) : Schema
    {
        $this->autocommit = $auto;

        return $this;
    }

    private function getSupportedDrivers() : array
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
