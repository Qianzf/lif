<?php

namespace Lif\Core\Strategy;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observer;
use Lif\Core\Intf\Strategy;
use Lif\Core\Factory\Ctl;
use Lif\Core\Factory\Web as WebFactory;

class Web extends Container implements Observer, Strategy
{
    protected $nameAsObsesrver = 'web';
    protected $request = null;
    protected $route   = null;
    protected $routes  = [];
    protected $aliases = [];

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
    }

    public function withRoutes($routeFiles)
    {
        if (!$routeFiles || !is_array($routeFiles)) {
            excp('Missing Routes files.');
        }

        $route = WebFactory::make('route');
        $route->run($this, $routeFiles);

        $this->route = $route;
        return $this;
    }

    protected function routeVerify($routeKey, $reqType, $route)
    {
        if (!isset($this->routes[$routeKey])) {
            client_error('Route `'.$route.'` not found.', 404);
        }

        if (!in_array($reqType, array_keys($this->routes[$routeKey]))) {
            client_error(
                '`'.$reqType.'` for route `'.$route.'` not found.',
                404
            );
        }
    }

    protected function handlerVerify($routeKey, $reqType, $route)
    {
        $handler = $this->routes[$routeKey][$reqType]['handle'];

        if (is_callable($handler)) {
            response($handler());
        } elseif (is_string($handler)) {
            $args = explode('@', $handler);
            if (count($args) !== 2 ||
                !($ctlName = trim(format_namespace($args[0]))) ||
                !($act = trim($args[1]))
            ) {
                excp(
                    'String type of route handler must be formatted with `Controller@action.`'
                    ."[{$reqType}('{$route}', '{$handler}')]",
                    415
                );
            }

            $ctl = Ctl::make($ctlName);
            $act = lcfirst($act);

            return call_user_func_array([
                $ctl,
                '__NON_EXISTENT_METHOD__'
            ], [$this, $act]);
        } else {
            excp(
                'Route handler must be Closure or String(`Controller@action`)',
                415
            );
        }
    }

    public function fire()
    {
        $this->request = $request = WebFactory::make('request');
        $request->addObserver($this);
        $route    = $request->route();
        $reqType  = $request->type();
        $routeKey = format_route_key($route);

        $this->routeVerify($routeKey, $reqType, $route);
        $this->handlerVerify($routeKey, $reqType, $route);
    }

    public function onRegistered($name, $type, $args)
    {
        if ('route' === $name) {
            $this->onRouteRegistered($type, $args);
        }
    }

    /**
     * [onRouteRegistered description]
     * @param  [type] $type  [description]
     * @param  [type] $route [description]
     * @return [type]        [description]
     */
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

        $handle = $route['namespace']
        ? $route['namespace'].'\\'.$route['bind']
        : $route['bind'];
        $this->aliases[$route['alias']] = $route['name'];
        $this->routes[$route['name']][$type] = [
            'handle' => $handle,
            'alias'  => $route['alias'],
            'middlewares' => $route['middlewares'],
        ];
    }

    public function nameAsObserver()
    {
        return $this->nameAsObsesrver;
    }

    public function routes()
    {
        return $this->routes;
    }

    public function aliases()
    {
        return $this->aliases;
    }

    public function route()
    {
        return $this->route;
    }

    public function request()
    {
        return $this->request;
    }
}
