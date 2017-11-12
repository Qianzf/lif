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

    public function __call($name, $args)
    {
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

        if (!($class = nsOf('storage', 'SQL\\'.ucfirst($this->getDriver())))
            || !class_exists($class)
        ) {
            excp('Schema class not exists: '.$class);
        }

        $statement = call_user_func_array(
            [(new $class), $name], $args
        );

        if ($statement) {
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
