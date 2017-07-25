<?php

namespace Lif\Core;

use Lif\Core\interfaces\Observable;

class Route implements Observable
{
    use \Lif\Core\Traits\Tol;

    private $routePath = null;

    private static $_observers = [];

    public static function __callStatic($name, $args)
    {
        $this->onCall($name, $args);
    }

    public function onCall($name, $args)
    {
        if (!$args || !isset($args[0]) || !isset($args[1]) || !$args[0] || !$args[1]) {
            $this->response(415, 'route name or handler can not be empty.');
        }

        foreach (self::$_observers as $observer) {
            $observer->onRegistered('route', strtoupper($name), $args);
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

    protected static function register($routePath = null)
    {
        $routePathAbsolute = $routePath ?? __DIR__.'/../route/';
        $app = new Route;
        foreach (scandir($routePathAbsolute) as $route) {
            $path = $routePathAbsolute.$route;
            if (is_file($path)) {
                include_once $path;
            }
        }
    }

    public static function run($instance)
    {
        self::addObserver($instance);
        self::register();
    }
}
