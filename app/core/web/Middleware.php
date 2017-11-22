<?php

// --------------------------------------
//     LiF web middlewares management
// --------------------------------------

namespace Lif\Core\Web;

use Lif\Core\Abst\Container;
use Lif\Core\Abst\Factory;
use Lif\Core\Intf\Observable;

class Middleware extends Container implements Observable
{
    use \Lif\Core\Traits\Observable;
    
    protected $name  = 'middleware';
    protected $argvs = [];
    protected $middlewares = [];    // key with object instance

    public function run($observer, $middlewares = [])
    {
        return $this
        ->addObserver($observer)
        ->passing($middlewares)
        ->done();
    }

    public function passing($middlewares): Middleware
    {
        $mdwrNS = nsOf('mdwr');
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                if (false !== mb_strpos($middleware, '.')) {
                    $nsArr = explode('.', $middleware);
                    array_walk($nsArr, function (&$item, $key) {
                        $item = ucfirst($item);
                    });

                    $middleware = implode('\\', $nsArr);
                } else {
                    $middleware = ucfirst($middleware);
                }

                $this->middlewares[$middleware] = (
                    $mdwr = Factory::make($middleware, $mdwrNS)
                );

                $this->argvs[$middleware][] = $mdwr->passing($this->app);
            }
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
