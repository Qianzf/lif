<?php

// -------------------------------------------
//     DB connection related base behavior
// -------------------------------------------

namespace Lif\Core\Intf;

use Lif\Core\Storage\LDO;

interface DBConn
{
    public function __construct(string $conn = null, string $flush = null);
    
    public function setConn(string $conn = null) : DBConn;

    public function setFlush(bool $flush) : DBConn;

    public function getConn() : string;

    public function getFlush() : bool;

    public function ldo() : LDO;
    
    public function db(LDO $ldo) : LDO;
}
