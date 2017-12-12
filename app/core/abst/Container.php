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

    public function responseOnUpdated(
        $model,
        string $uri = null,
        \Closure $callback = null
    )
    {
        $uri = $uri ?? $this->route;

        if (! $model->isAlive()) {
            share_error_i18n('OBJECT_NOT_FOUND');
            return redirect($uri);
        }

        if (!empty_safe($err = $model->save($this->request->posts()))
            && is_numeric($err)
            && ($err >= 0)
        ) {
            if ($err > 0) {
                $status = 'UPDATE_OK';
            } else {
                $status = 'UPDATED_NOTHING';
            }
        } else {
            $status = 'UPDATE_FAILED';
        }

        $err = is_integer($err) ? null : L($err);

        if ($callback) {
            $callback();
        }

        share_error(L($status, $err));

        redirect($uri);
    }

    public function responseOnCreated(
        $model,
        string $uri,
        \Closure $callback = null
    )
    {
        if (($status = $model->create($this->request->posts()))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
        } else {
            $msg    = L('CREATED_FAILED', L($status));
            $status = 'new';
        }

        if ($callback) {
            $callback();
        }

        share_error_i18n($msg);

        return redirect(uri($uri, [$status]));
    }

    public function validate(array $data, array $rules)
    {
        $prepare = validate($data, $rules);

        if (true !== $prepare && ('web' == context())) {
            share('__error', sysmsg($prepare));
            
            return redirect($this->route);
        }

        return $data;
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
