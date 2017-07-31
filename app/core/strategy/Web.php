<?php

namespace Lif\Core\Strategy;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observer;
use Lif\Core\Intf\Strategy;
use Lif\Core\Factory\Ctl;
use Lif\Core\Factory\Web as WebFactory;

class Web extends Container implements Observer, Strategy
{
    use \Lif\Core\Traits\WebGetter;

    protected $nameAsObsesrver = 'web';
    protected $request = null;    // request object
    protected $route   = null;    // current route name
    protected $headers = [];      // current request HTTP headers
    protected $params  = [];      // current request params
    protected $_route  = null;    // route object
    protected $routes  = [];      // all routes and their bindings
    protected $aliases = [];      // all routes and their aliases

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

        $this->_route = WebFactory::make('route');
        $this->_route->run($this, $routeFiles);

        return $this;
    }

    public function findRoute($key, $type, $name)
    {
        if (!isset($this->routes[$key])) {
            client_error('Route `'.$route.'` not found.', 404);
        }

        if (!in_array($type, array_keys($this->routes[$key]))) {
            client_error(
                '`'.$type.'` for route `'.$name.'` not found.',
                404
            );
        }

        return $this;
    }

    public function handle()
    {
        $route    = $this->request->route();
        $reqType  = $this->request->type();
        $routeKey = format_route_key($route);

        $this->findRoute($routeKey, $reqType, $route);

        $handler = $this->routes[$routeKey][$reqType]['handle'];

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
                '__NON_EXISTENT_METHOD__'
            ], [$this, $act]);
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        return $this;
    }

    public function fire()
    {
        $this->request = WebFactory::make('request');
        $this->request->run($this);

        return $this;
    }

    public function onRegistered($name, $type = null, $args = null)
    {
        $onRegistered = 'on'.ucfirst($name).'Registered';

        $this->$onRegistered($type, $args);

        return $this;
    }

    protected function onRequestRegistered()
    {
        $this->route   = $this->request->route;
        $this->params  = $this->request->params;
        $this->headers = $this->request->headers;

        return $this;
    }

    protected function onRouteRegistered($type, $route)
    {
        if (isset($this->routes[$route['name']][$type])) {
            excp(
                'Duplicate definition on `'.
                get_raw_route($route['name']).
                '` of `'.$type.'`.'
            );
        }

        if (in_array($route['alias'], array_keys($this->aliases))) {
            excp(
                'Duplicate route alias `'.
                $route['alias'].
                '` for `'.
                get_raw_route($route['name'])
            );
        }

        $this->aliases[$route['alias']]      = $route['name'];
        $this->routes[$route['name']][$type] = [
            'handle'      => $route['handle'],
            'alias'       => $route['alias'],
            'middlewares' => $route['middlewares'],
        ];

        return $this;
    }

    public function nameAsObserver()
    {
        return $this->nameAsObsesrver;
    }
}
