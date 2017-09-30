<?php

namespace Lif\Core\Abst;

abstract class Container
{
    use \Lif\Core\Traits\MethodNotExists;
    
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

        return $this->__callSafe($method, $params);
    }

    public function __closureSafe(\Closure $handle, array $params = [])
    {
        try {
            return call_user_func_array($handle, $params);
        } catch (\ArgumentCountError $e) {
            return $this->__closureSafe(
                $handle,
                $this->handleArgumentCountError($e)
            );
        } catch (\TypeError $e) {
            return $this->__closureSafe(
                $handle,
                $this->handleTypeError($e, $params)
            );
        } catch (\Error $e) {
            exception($e);
        } finally {
        }
    }

    public function __callSafe($method, array $params)
    {
        try {
            return call_user_func_array([
                $this,
                $method
            ], $params);
        } catch (\ArgumentCountError $e) {
            return $this->__callSafe(
                $method,
                $this->handleArgumentCountError($e)
            );
        } catch (\TypeError $e) {
            return $this->__callSafe(
                $method,
                $this->handleTypeError($e, $params)
            );
        } catch (\Error $e) {
            exception($e);
        } finally {
        }
    }

    protected function handleArgumentCountError(\ArgumentCountError $e) : array
    {
        if (!preg_match(
            '/^Too\ few\ arguments\ to\ function ([\\\\\w]+)::.*and\ exactly\ (\d)+\ expected$/u',
            $e->getMessage(),
            $matches
        )
        // || (exists($matches, 1) != get_class($this))
        || (intval($missingArgsCnt = exists($matches, 2)) < 1)
        ) {
            excp($e->getMessage());
        }

        // !!! Do not forge `null`, because `isset(null)` is false
        $forgeArgs = array_fill(0, $missingArgsCnt, false);

        return $forgeArgs;
    }

    protected function handleTypeError(\TypeError $e, array $params) : array
    {
        if (!preg_match(
            '/Argument\ (\d+) passed to ([\\\\\w]+)::.*must\ be\ an?\ (.*)\ of\ ([\w\\\\]*),/u',
            $e->getMessage(),
            $matches
        )
        || !(true &&
            exists($matches, 1) &&
            exists($matches, 2) &&
            exists($matches, 3) &&
            exists($matches, 4)
        ) ||
            ('instance' != $matches[3]) ||
            !(($argOrder = intval($matches[1])) == $matches[1])
        ) {
            excp($e->getMessage());
        }

        if (!class_exists($matches[4])) {
            excp(
                'Class `'.$matches[4].'` not exists.'
            );
        }

        // !!! `isset(false)` is true
        if (!isset($params[--$argOrder])) {
            exception($e);
            excp(
                'Missing params from route definition.'
            );
        }

        // repalace the type error arg with object
        $params[$argOrder] = new $matches[4](
            $params[$argOrder]
        );

        return $params;
    }

    public function validate(array $data, array $rules)
    {
        $prepare = validate($data, $rules);

        if (true !== $prepare && ('web' == context())) {
            share('__error', sysmsg($prepare));
            redirect('/'.$this->route);
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
