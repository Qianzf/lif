<?php

// -----------------------------------
//     SQL database schema builder
// -----------------------------------

namespace Lif\Core\Storage\SQL;

class Schema implements \Lif\Core\Intf\DBConn
{
    use \Lif\Core\Traits\MethodNotExists;
    use \Lif\Core\Traits\WithDB;

    private $driver    = null;
    private $supported = [
        'mysql',
        'sqlite',
    ];

    // Rewrite constructor in trait WithDB
    public function __construct(string $conn = null, string $flush = null)
    {
        $this->setConn($conn);

        $this->flush = $flush;

        $this->db();

        if (!($this->driver = $this->_conn['driver'] ?? null)
            || !in_array($this->driver, $this->getSupportedDrivers())
        ) {
            excp(
                'Database schema of driver not supported yet: '
                .$this->driver
            );
        }
    }

    public function __call($name, $args)
    {
        if (!($class = nsOf('storage', 'SQL\\'.ucfirst($this->driver)))
            || !class_exists($class)
        ) {
            excp('Schema class not exists: '.$class);
        }

        return call_user_func_array([(new $class), $name], $args);
    }

    private function addSupportDriver(string $driver) : Schema
    {
        if (! in_array($driver, $this->supported)) {
            $this->supported[] = $driver;
        }

        return $this;
    }

    private function getSupportedDrivers() : array
    {
        return $this->supported;
    }
}
