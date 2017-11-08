<?php

// --------------------------
//     LiF base container
// --------------------------

namespace Lif\Core\Abst;

abstract class Container
{
    use \Lif\Core\Traits\MethodNotExists;
    
    protected $app = null;
    private $__recursion_ace = 0;    // Avoid infinite recursion call
    private $__recursion_te  = 0;    // Avoid infinite recursion call

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

    public function __callableSafe($handle, array $params = [])
    {
        if (! is_callable($handle)) {
            excp('Un-callable handler.');
        }

        try {
            $result = call_user_func_array($handle, $params);
            $this->__recursion_ace = $this->__recursion_te = 0;

            return $result;
        } catch (\ArgumentCountError $e) {
            return $this->__callableSafe(
                $handle,
                $this->handleArgumentCountError($e, $params)
            );
        } catch (\TypeError $e) {
            return $this->__callableSafe(
                $handle,
                $this->handleTypeError($e, $params)
            );
        } catch (\Error $e) {
            exception($e);
        } finally {
        }
    }

    public function __methodSafe($method, array $params)
    {
        try {
            $result = call_user_func_array([
                $this,
                $method
            ], $params);
            $this->__recursion_ace = $this->__recursion_te = 0;

            return $result;
        } catch (\ArgumentCountError $e) {
            return $this->__methodSafe(
                $method,
                $this->handleArgumentCountError($e, $params)
            );
        } catch (\TypeError $e) {
            return $this->__methodSafe(
                $method,
                $this->handleTypeError($e, $params)
            );
        } catch (\Error $e) {
            exception($e);
        } catch (\Exception $e) {
            exception($e);
        } catch (\Throwable $e) {
            exception($e);
        } finally {
        }
    }

    protected function handleArgumentCountError(
        \ArgumentCountError $e,
        array $params
    ) : array {
        if ((3 < ++$this->__recursion_ace)
        || !preg_match(
            '/^Too\ few\ arguments\ to\ function ([\\\\\w]+)::.* (\d)+\ passed and\ exactly\ (\d)+\ expected$/u',
            $e->getMessage(),
            $matches
        )
        // || (exists($matches, 1) != get_class($this))
        || (($expectedArgsCnt = intval(exists($matches, 3))) < 1)
        || (($missingArgsCnt  = (
            $expectedArgsCnt - intval(exists($matches, 2)))
            ) < 1)
        ) {
            excp($e->getMessage());
        }

        // !!! Do not forge `null`, because `isset(null)` is false
        $forgeArgs = array_fill(0, $missingArgsCnt, false);

        // Avoid replace passed parameters
        array_push($params, ...$forgeArgs);    // use `...` to extract array
        
        return $params;
    }

    protected function handleTypeError(
        \TypeError $e,
        array $params
    ) : array {
        if ((3 < ++$this->__recursion_te)
        || !preg_match(
            '/Argument\ (\d+) passed to ([\\\\\w]+)::.*must\ be\ an?\ (.*)\ of\ ([\w\\\\]*),/u',
            $e->getMessage(),
            $matches
        )
        || !exists($matches, 1)
        || !exists($matches, 2)
        || !exists($matches, 3)
        || !exists($matches, 4)
        || ('instance' != $matches[3])
        || (($argOrder = intval($matches[1])) != $matches[1])
        ) {
            excp($e->getMessage());
        }

        if (! class_exists($matches[4])) {
            excp(
                'Class `'.$matches[4].'` not exists.'
            );
        }

        // !!! `isset(false)` is true
        if (! isset($params[--$argOrder])) {
            exception($e);
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
            redirect($this->route);
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
