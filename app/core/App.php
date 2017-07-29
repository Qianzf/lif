<?php

namespace Lif\Core;

use Lif\Core\Factory\Strategy;
use Lif\Core\Abst\Container;

class App extends Container
{
    protected $strategy = null;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        init();

        $this->setStrategy();
    }

    private function setStrategy()
    {
        $this->strategy = Strategy::make(context());
    }

    public function __call($method, $args)
    {
        return call_user_func_array([
            $this->strategy,
            $method
        ], $args);
    }
}
