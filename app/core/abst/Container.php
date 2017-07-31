<?php

namespace Lif\Core\Abst;

abstract class Container
{
    protected $app = null;

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } elseif (method_exists($this, $name)) {
            return $this->$name();
        } elseif (method_exists($this->app, $name)) {
            return $this->app->$name();
        }
    }

    public function NONEXISTENTMETHODOFCONTROLLER($args)
    {
        if (!isset($args[0]) || !is_object($args[0])) {
            excp(
                'Missing strategy object in params pass to controller.'
            );
        }
        if (!isset($args[1]) || !$args[1] || !is_string($args[1])) {
            excp(
                'Missing action in params pass to controller.'
            );
        }

        $this->app = $args[0];
        return $this->{$args[1]}();
    }

    public function __call($name, $args)
    {
        if (method_exists($this->app, $name)) {
            return $this->app->$name($args);
        } else {
            throw new \Lif\Core\Excp\MethodNotFound(static::class, $name);
        }
    }

    public function db($conn = null)
    {
        return db($conn);
    }

    public function dbconns($conn = null)
    {
        return db_conns($conn);
    }
}
