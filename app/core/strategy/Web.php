<?php

namespace Lif\Core\Strategy;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observer;
use Lif\Core\Intf\Strategy;
use Lif\Core\Factory\Ctl;
use Lif\Core\Factory\Web as WebFcty;

class Web extends Container implements Observer, Strategy
{
    protected $name = 'web';

    public function __construct()
    {
        $this->app = &$this;
        $this->loadWebHelpers();
    }

    protected function loadWebHelpers()
    {
        $webHelpers = pathOf('aux').'web.php';
        
        if (!file_exists($webHelpers)) {
            excp('Web helper file does not exists.');
        }

        require_once $webHelpers;

        return $this;
    }

    public function withRoutes($routeFiles)
    {
        if (!$routeFiles || !is_array($routeFiles)) {
            excp('Missing Routes files.');
        }

        $this->_route = WebFcty::make('route');
        $this->_route->run($this, $routeFiles);

        return $this;
    }

    public function findRoute()
    {
        $name  = $this->request->route();
        $type  = $this->request->type();
        $key   = format_route_key($name);

        if (!isset($this->routes[$key])) {
            client_error('Route `'.$name.'` not found.', 404);
        }

        if (!in_array($type, array_keys($this->routes[$key]))) {
            client_error(
                '`'.$type.'` for route `'.$name.'` not found.',
                404
            );
        }

        if (!isset($this->routes[$key][$type]['handle'])) {
            client_error(
                'Handler for route `'.$name.'` (`'.$type.'`) not found.',
                404
            );
        }

        return $this->routes[$key][$type]['handle'];
    }

    public function handle()
    {
        $handler = $this->findRoute();

        if (is_callable($handler)) {
            return response($handler());
        } elseif (is_string($handler)) {
            $args = explode('@', $handler);
            if (count($args) !== 2 ||
                !($ctlName = trim(format_namespace($args[0]))) ||
                !($act = trim($args[1]))
            ) {
                throw new \Lif\Core\Excp\IllegalRouteDefinition(2);
            }

            $ctl = Ctl::make($ctlName);
            $act = lcfirst($act);

            return call_user_func_array([
                $ctl,
                'NONEXISTENTMETHODOFCONTROLLER'
            ], [$this, $act]);
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        return $this;
    }

    public function fire()
    {
        $this->request = WebFcty::make('request');
        $this->request->run($this);

        return $this;
    }

    public function listen($name)
    {
        $listen = 'listenOn'.ucfirst($name);

        return $this->$listen();
    }

    protected function listenOnRequest()
    {
        return $this->handle();
    }

    protected function listenOnRoute()
    {
        return $this->fire();
    }

    public function name()
    {
        return $this->name;
    }

    public function routes()
    {
        return $this->_route->routes;
    }

    public function aliases()
    {
        return $this->_route->aliases;
    }

    public function route()
    {
        return $this->request->route;
    }

    public function params()
    {
        return $this->request->params;
    }

    public function headers()
    {
        return $this->request->headers;
    }

    public function _route()
    {
        return $this->_route;
    }

    public function request()
    {
        return $this->request;
    }
}
