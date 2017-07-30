<?php

namespace Lif\Core\Storage;

class LDO extends \PDO
{
    public function conns($conn = null)
    {
        return db_conns($conn);
    }

    public function __get($name)
    {
        return $this->$name();
    }

    public function __call($name, $args)
    {
        excp(
            'Method `'.$name.'()` not exists in '.(static::class)
        );
    }

    public static function __callStatic($name, $args)
    {
        excp(
            'Static method `'.$name.'()` not exists in '.(static::class)
        );
    }
}
