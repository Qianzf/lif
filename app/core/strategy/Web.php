<?php

namespace Lif\Core\Strategy;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observer;
use Lif\Core\Intf\Strategy;
use Lif\Core\Factory\Ctl;
use Lif\Core\Factory\Web as WebFactory;

class Web extends Container implements Observer, Strategy
{
    public $nameAsObsesrver = 'web';
    public $request = null;
    public $route   = null;
    public $routes  = [];

    public function withRoutes($routeFiles)
    {
        if (!$routeFiles || !is_array($routeFiles)) {
            api_exception('Missing Routes files.');
        }

        $route = WebFactory::make('route');
        $route->run($this, $routeFiles);

        $this->route = $route;
        return $this;
    }

    protected function routeVerify($routeKey, $reqType, $route)
    {
        if (!isset($this->routes[$routeKey])) {
            api_exception('Route `'.$route.'` not found.', 404);
        }

        if (!in_array($reqType, array_keys($this->routes[$routeKey]))) {
            api_exception(
                '`'.$reqType.'` for route `'.$route.'` not found.',
                404
            );
        }
    }

    protected function handlerVerify($routeKey, $reqType, $route)
    {
        $handler = $this->routes[$routeKey][$reqType];

        if (is_callable($handler)) {
            response($handler());
        } elseif (is_string($handler)) {
            $args = explode('@', $handler);
            if (count($args) !== 2 ||
                !($ctlName = trim($args[0])) ||
                !($act = trim($args[1]))
            ) {
                api_exception(
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
            api_exception(
                'Route handler must be Closure or String(`Controller@action`)',
                415
            );
        }
    }

    public function fire()
    {
        $this->request = $request = WebFactory::make('request');

        $route    = $request->route();
        $reqType  = $request->type();
        $routeKey = format_route_key($route);

        $this->routeVerify($routeKey, $reqType, $route);
        $this->handlerVerify($routeKey, $reqType, $route);
    }

    public function getNameAsObserver()
    {
        return $this->nameAsObsesrver;
    }

    public function onRegistered($name, $type, $args)
    {
        if ('route' === $name) {
            $this->onRouteRegistered($type, $args);
        }
    }

    public function onRouteRegistered($type, $args)
    {
        $routeKey    = format_route_key($args[0]);
        $routeType   = $type;
        $routeHandle = $args[1];

        if (isset($this->routes[$routeKey][$routeType])) {
            api_exception(
                'Duplicate definition on `'.$args[0].'` of `'.$routeType.'`.'
            );
        }

        $this->routes[$routeKey][$routeType] = $routeHandle;
    }
}
