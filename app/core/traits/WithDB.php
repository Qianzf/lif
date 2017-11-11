<?php

// ---------------------------------------
//     Mix-in DB connection into class
// ---------------------------------------

namespace Lif\Core\Traits;

use Lif\Core\Intf\DBConn;
use Lif\Core\Storage\LDO;

trait WithDB
{
    protected $db       = null;    // Database connection object
    protected $flush    = null;    // Flush database connection flag
    protected $conn     = null;    // Database connection name
    protected $_conn    = [];      // Database connection configs

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
        if ($db) {
            $this->db = $db;
        }
        
        return $this->db ?? ($this->db = $this->ldo());
    }

    public function setConn(string $conn = null): DBConn
    {
        $this->_conn = ($this->conn = $conn)
        ? (conf('db')['conns'][$conn] ?? [])
        : [];

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
    
    public function getConn() : string
    {
        return $this->conn;
    }
}
