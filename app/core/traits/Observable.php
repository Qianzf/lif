<?php

// --------------------------------------------
//     This trait is used for classes who:
//     - implements \Lif\Core\Intf\Observable
//     - extends \Lif\Core\Abst\Container
// --------------------------------------------

namespace Lif\Core\Traits;

trait Observable
{
    protected $observers = [];

    public function addObserver($observer)
    {
        if (!is_object($observer)) {
            excp(
                'Observer must belongs to an object.'
            );
        }
        if (!method_exists($observer, 'name')) {
            excp(
                'Observer must has a public function `name()`.'
            );
        }

        if ('web' === $observer->name()) {
            $this->setApp($observer);
        }

        $this->observers[] = $observer;

        return $this;
    }

    protected function setApp($obj)
    {
        $this->app = &$obj;

        return $this;
    }

    protected function trigger()
    {
        foreach ($this->observers as $observer) {
            $observer->listen($this->name);
        }

        return $this;
    }

    public function name()
    {
        return $this->name;
    }
}
