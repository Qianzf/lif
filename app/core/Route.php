<?php

namespace Lif\Core;

use Lif\Core\interfaces\Observable;

class Route implements Observable
{
    use \Lif\Core\Traits\Tol;

    private static $_observers = [];

    public static function __callStatic($name, $args)
    {
        $this->onCall($name, $args);
    }

    public function onCall($name, $args)
    {
        if (!$args || !isset($args[0]) || !isset($args[1]) || !$args[0] || !$args[1]) {
            $this->jsonResponse(415, 'route name or handler can not be empty.');
        }

        foreach (self::$_observers as $observer) {
            $observer->onRouteRegistered(strtoupper($name), $args);
        }
    }

    public function __call($name, $args)
    {
        $this->onCall($name, $args);
    }

    public static function addObserver($observer)
    {
        self::$_observers[] = $observer;
    }

    public static function register()
    {
        $app = new Route;
        require_once __DIR__.'/../route.php';
    }
}
