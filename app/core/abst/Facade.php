<?php

namespace Lif\Core\Abst;

abstract class Facade
{
    protected static $name      = null;
    protected static $proxy     = null;
    protected static $singleton = true;

    protected static function getName()
    {
        if (empty_safe(static::$name)) {
            return strtoupper(ns2classname(static::class));
        }

        return static::$name;
    }

    protected static function __getProxy()
    {
        if (empty_safe(static::$proxy)) {
            if (method_exists(get_called_class(), 'getProxy')
                && ($proxy = static::getProxy())
            ) {
                return $proxy;
            }

            return null;
        }

        return static::$proxy;
    }

    public static function __callStatic($method, $params)
    {
        if (! class_exists($proxy = static::__getProxy())) {
            excp('Facade not exists: '.($proxy ?? '(empty)'));
        }

        $object = static::$singleton
        ? singleton(static::getName(), function () use ($proxy) {
            return $proxy;
        })
        : (new $proxy);

        return call_user_func_array([$object, $method], $params);
    }
}
