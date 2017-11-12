<?php

// --------------------------------
//     PDO customization staffs
// --------------------------------

namespace Lif\Core\Storage;

class LDO extends \PDO
{
    protected $conn = null;

    public function setConn(string $conn = null) : LDO
    {
        $this->conn = $conn;

        return $this;
    }

    public function getConn() : string
    {
        return $this->conn;
    }
}
