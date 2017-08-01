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

        return $this->__callSave($method, $params);
    }

    public function __callSave($method, $params)
    {
        try {
            return call_user_func_array([
                $this,
                $method
            ], $params);
        } catch (\ArgumentCountError $e) {
            excp($e->getMessage());
        } catch (\TypeError $e) {
            if (!preg_match(
                '/Argument\ (\d+).*must\ be\ an?\ (.*)\ of\ ([\w\\\\]*),/',
                $e->getMessage(),
                $matches
            ) || !(true &&
                exists($matches, 1) &&
                exists($matches, 2) &&
                exists($matches, 3)
            ) ||
                ('instance' != $matches[2]) ||
                !(($argOrder = intval($matches[1])) == $matches[1])
            ) {
                excp($e->getMessage());
            }

            if (!class_exists($matches[3])) {
                excp(
                    'Class `'.$matches[3].'` not exists.'
                );
            }

            if (!exists($params, --$argOrder)) {
                excp(
                    'Missing params from route definition.'
                );
            }

            // repalace the type error arg with object
            $params[$argOrder] = new $matches[3](
                $params[$argOrder]
            );

            return $this->__callSave($method, $params);
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
