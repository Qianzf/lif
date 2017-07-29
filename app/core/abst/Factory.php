<?php

namespace Lif\Core\Abst;

abstract class Factory
{
    protected static $namespace = null;

    public static function make($name)
    {
        // use `static` instead of `self`
        // because we need the namespace of sub class

        $class = static::$namespace.ucfirst($name);
        return new $class;
    }
}
