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

    public function run($observer, $middlewares = [])
    {
        return $this
        ->addObserver($observer)
        ->middlewares($middlewares)
        ->trigger();
    }

    public function middlewares($middlewares)
    {
        $mdwrNS = nsOf('mdwr');
        foreach ($middlewares as $middleware) {
            $this->argvs[$middleware][] = (
                Factory::make($middleware, $mdwrNS)
            )->handle($this->app);
        }

        return $this;
    }

    public function argvs()
    {
        return $this->argvs;
    }
}
