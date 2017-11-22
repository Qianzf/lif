<?php

// --------------------------
//     LiF base container
// --------------------------

namespace Lif\Core\Abst;

abstract class Container
{
    use \Lif\Core\Traits\MethodNotExists;
    use \Lif\Core\Traits\DI;
    
    protected $app = null;

    public function __construct()
    {
    }
    
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

    public function __lif__($obj, $method, array $params = [])
    {
        if (!isset($obj) || !is_object($obj)) {
            excp(
                'Missing strategy object.'
            );
        }
        if (!isset($method) || !$method || !is_string($method)) {
            excp(
                'Missing or bad action.'
            );
        }

        $this->app = $obj;

        return $this->__methodSafe($method, $params);
    }

    public function validate(array $data, array $rules)
    {
        $prepare = validate($data, $rules);

        if (true !== $prepare && ('web' == context())) {
            share('__error', sysmsg($prepare));
            
            return redirect($this->route);
        }

        return $prepare;
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
