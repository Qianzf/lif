<?php

namespace Lif\Core\Intf;

interface SQLSchemaMaster extends DBConn
{
    public function createWorker() : SQLSchemaWorker;
    
    public function commit();

    public function exec(string $statement);
    
    public function query(string $statement);

    public function addSupportDriver(string $driver) : SQLSchemaMaster;

    public function getSupportedDrivers() : array;
}
