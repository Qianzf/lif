<?php

namespace Lif\Core\Abst;

use Lif\Core\Factory\Storage;

use Lif\Core\Intf\App;

abstract class Container
{
    protected $app = null;

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        if ('db' === strtolower($name)) {
            if (!$this->pdo) {
                $this->pdo = (Storage::make('db'))->pdo;
            }
            
            return $this->pdo;
        }

        if (method_exists($this, $name)) {
            return $this->$name();
        } elseif (method_exists($this->app, $name)) {
            return $this->app->$name();
        }
    }
}
