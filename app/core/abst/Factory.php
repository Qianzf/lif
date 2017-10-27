<?php

namespace Lif\Core\Abst;

abstract class Factory
{
    protected static $namespace = '';

    public static function make(
        string $name,
        string $namespace = '',
        array $data = []
    ) {
        if (!$name && !$namespace) {
            excp('Missing class name.');
        }
        if (! $namespace) {
            // use `static` instead of `self`
            // because we need the namespace of sub class
            $namespace = static::$namespace;
        }
        
        $class = $namespace.ucfirst($name);

        if (! class_exists($class)) {
            excp('Class `'.$class.'` not exists.');
        }

        return new $class($data);
    }

    public static function fetch(
        string $class,
        string $method,
        array $args = []
    ) {
        return (static::$namespace.ucfirst($class))::$method($args);
    }
}
