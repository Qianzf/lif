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
        } elseif (true &&
            isset($this->app->request) &&
            is_object($this->app->request) &&
            method_exists($this->app->request, $name)
        ) {
            return $this->app->request->$name();
        } else {
            return $this->$name();
        }
    }

    public function NONEXISTENTMETHODOFCONTROLLER($obj, $method, $params)
    {
        if (!isset($obj) || !is_object($obj)) {
            excp(
                'Missing strategy object in params pass to controller.'
            );
        }
        if (!isset($method) || !$method || !is_string($method)) {
            excp(
                'Missing action in params pass to controller.'
            );
        }

        $this->app = $obj;

        try {
            return call_user_func_array([
                $this,
                $method
            ], $params);
        } catch (\ArgumentCountError $e) {
            excp($e->getMessage());
        } finally {
        }
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
