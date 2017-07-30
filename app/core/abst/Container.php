<?php

namespace Lif\Core\Abst;

abstract class Container
{
    protected $app = null;

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        if (method_exists($this, $name)) {
            return $this->$name();
        } elseif (method_exists($this->app, $name)) {
            return $this->app->$name();
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
