<?php

namespace Lif\Core\Abst;

use Lif\Core\Factory\Storage;

abstract class Container
{
    public function __get($name)
    {
        if (isset($this->strategy->$name)) {
            return $this->strategy->$name;
        }

        if ('db' === strtolower($name)) {
            if (!$this->pdo) {
                $this->pdo = (Storage::make('db'))->pdo;
            }
            
            return $this->pdo;
        }
    }
}
