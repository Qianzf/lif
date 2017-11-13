<?php

namespace Lif\Core\Factory;

class Singleton
{
    private static $singletons = [];

    private function __construct()
    {
    }

    public static function set(
        string $key, 
        \Closure $callable,
        bool $flush = null
    ) {
        $singleton = self::$singletons[$key] ?? null;

        if (!$singleton || $flush) {
            if (is_object($singleton = $callable())) {
            } elseif (is_string($singleton)) {
                $singleton = (new $singleton);
            } else {
                excp('Illegal singleton class:'.stringify($singleton));
            }
        }

        return $singleton;
    }

    public static function get(string $key)
    {
        return self::$singletons[$key] ?? (
            new class($key)
            {
                public function __construct(string $key)
                {
                    excp("Class for singleton `{$key}` not exists.");
                }
            }
        );
    }
}
