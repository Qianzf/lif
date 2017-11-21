<?php

namespace Lif\Core\Traits;

trait DI
{
    private $__recursion_ace = 0;    // Avoid infinite recursion call
    private $__recursion_te  = 0;    // Avoid infinite recursion call

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
}
