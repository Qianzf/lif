<?php

namespace Lif\Core\Web;

use Lif\Core\Abst\Container;
use Lif\Core\Abst\Factory;
use Lif\Core\Intf\Observable;

class Middleware extends Container implements Observable
{
    use \Lif\Core\Traits\Observable;
    
    protected $name  = 'middleware';
    protected $argvs = [];
    protected $middlewares = [];

    public function run($observer, $middlewares = [])
    {
        return $this
        ->addObserver($observer)
        ->execute($middlewares)
        ->done();
    }

    public function execute($middlewares)
    {
        $this->middlewares = $middlewares;

        $mdwrNS = nsOf('mdwr');
        foreach ($middlewares as $middleware) {
            $this->argvs[$middleware][] = (
                Factory::make($middleware, $mdwrNS)
            )->handle($this->app);
        }

        return $this;
    }

    public function all()
    {
        return $this->middlewares;
    }

    public function argvs()
    {
        return $this->argvs;
    }
}
