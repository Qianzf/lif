<?php

namespace Lif\Core\Traits;

trait MethodNotExists
{
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
