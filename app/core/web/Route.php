<?php

namespace Lif\Core\Web;

use Lif\Core\Intf\Observable;

class Route implements Observable
{
    protected $_observers = [];
    public $routeFiles = [];

    public function onCall($name, $args)
    {
        if (!$args || !isset($args[0]) || !isset($args[1]) || !$args[0] || !$args[1]) {
            response(415, 'route name or handler can not be empty.');
        }

        foreach ($this->_observers as $observer) {
            $observer->onRegistered('route', strtoupper($name), $args);
        }
    }

    public static function __callStatic($name, $args)
    {
        $this->onCall($name, $args);
    }

    public function __call($name, $args)
    {
        $this->onCall($name, $args);
    }

    public function addObserver($observer)
    {
        if (!is_object($observer)) {
            api_exception(
                'Observer must belongs to an object.'
            );
        }
        if (!method_exists($observer, 'getNameAsObserver')) {
            api_exception(
                'Observer must has a public function `getNameAsObserver`.'
            );
        }

        $this->_observers[$observer->getNameAsObserver()] = $observer;
    }

    protected function register($routeFiles)
    {
        $routePath = pathOf('route');
        foreach ($routeFiles as $route) {
            $path = $routePath.$route.'.php';
            $file = pathinfo($path);
            if (is_file($path) &&
                isset($file['extension']) &&
                ('php' === $file['extension'])
            ) {
                $this->routeFiles[] = $path;
                include_once $path;
            }
        }
    }

    public function run($observer, $routeFiles)
    {
        $this->addObserver($observer);
        $this->register($routeFiles);
    }
}
