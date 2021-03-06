<?php

// ---------------------------------------
//     Mix-in DB connection into class
// ---------------------------------------

namespace Lif\Core\Traits;

use Lif\Core\Intf\DBConn;
use Lif\Core\Storage\LDO;

trait WithDB
{
    protected $db     = null;    // Database connection object
    protected $flush  = null;    // Flush database connection flag
    protected $conn   = null;    // Database connection name
    protected $driver = null;    // Database driver 

    public function __construct(string $conn = null, string $flush = null)
    {
        $this->setConn($conn);

        $this->flush = $flush;

        $this->db();
    }

    public function ldo() : LDO
    {
        $ldo = ldo($this->conn, $this->flush);
        
        $this->setConn($ldo->getConn());

        return $ldo;
    }

    public function db(LDO $db = null) : LDO
    {
        return $this->db = $this->ldo();
    }

    public function setConn(string $conn = null): DBConn
    {
        $this->conn = $conn;

        return $this;
    }

    public function setFlush(bool $flush) : DBConn
    {
        $this->flush = $flush;

        return $this;
    }

    public function getFlush() : bool
    {
        return $this->flush;
    }

    public function getDb()
    {
        return $this->db;
    }
    
    public function getConn()
    {
        return $this->conn;
    }

    public function getDriver()
    {
        return $this->driver
        ?? (
            ($this->driver = conf('db')['conns'][$this->conn]['driver'])
            ?? null
        );
    }
}
