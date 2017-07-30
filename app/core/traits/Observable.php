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
        if (!method_exists($observer, 'nameAsObserver')) {
            excp(
                'Observer must has a public function `nameAsObserver()`.'
            );
        }

        if ('web' === $observer->nameAsObserver()) {
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
}
