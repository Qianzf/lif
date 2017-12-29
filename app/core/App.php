<?php

namespace Lif\Core;

class App
{
    protected $strategy = null;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        init();

        $this->strategy = \Lif\Core\Abst\Factory::make(
            context(),
            nsOf('strategy')
        );
    }

    public function __call($method, $args)
    {
        return call_user_func_array([
            $this->strategy,
            $method
        ], $args);
    }
}
