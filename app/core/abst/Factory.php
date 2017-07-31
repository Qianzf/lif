<?php

namespace Lif\Core\Abst;

abstract class Factory
{
    protected static $namespace = null;

    public static function make($name, $namespace = null)
    {
        if (is_null($namespace)) {
            // use `static` instead of `self`
            // because we need the namespace of sub class
            $namespace = static::$namespace;
        }
        
        $class = $namespace.ucfirst($name);

        if (!class_exists($class)) {
            excp('Class `'.$class.'` not exists.');
        }

        return new $class;
    }

    public static function fetch($class, $method, $args = null)
    {
        return (static::$namespace.ucfirst($class))::$method($args);
    }
}
