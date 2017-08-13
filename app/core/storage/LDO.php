<?php

namespace Lif\Core\Storage;

class LDO extends \PDO
{
    use \Lif\Core\Traits\MethodNotExists;
    
    public function conns($conn = null)
    {
        return db_conns($conn);
    }

    public function __get($name)
    {
        return $this->$name();
    }
}
