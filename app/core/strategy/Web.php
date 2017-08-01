<?php

namespace Lif\Core\Strategy;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observer;
use Lif\Core\Intf\Strategy;
use Lif\Core\Factory\Ctl;
use Lif\Core\Factory\Web as WebFcty;

class Web extends Container implements Observer, Strategy
{
    protected $name        = 'web';
    protected $_route      = null;
    protected $request     = null;
    protected $middleware  = null;

    private $listenHandleMap = [
        'route'      => 'fire',
        'request'    => 'handle',
        'middleware' => 'execute',
    ];

    // Temporary stacks for route params
    private $vars = [];

    public function __construct()
    {
        $this->app = &$this;
        $this->load();
    }

    // load web helpers
    protected function load()
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

    // escape route key for variables-bound scenario
    public function escape($key)
    {
        if (!isset($this->routes[$key])) {
            $arr = array_reverse(array_filter(explode('.', $key)));

            foreach ($arr as $idx => $val) {
                $arr[$idx]    = '{?}';
                $this->vars[] = $val;
                $escapeKey = implode('.', array_reverse($arr));
                if (isset($this->routes[$escapeKey])) {
                    return $escapeKey;
                }
            }

            client_error('Route `'.$this->request->route.'` not found.', 404);
        }

        return $key;
    }

    // handle request
    public function handle()
    {
        $name  = $this->request->route();
        $type  = $this->request->type();
        $key   = $this->escape(format_route_key($name));

        if (!in_array($type, array_keys($this->routes[$key]))) {
            client_error(
                '`'.$type.'` for route `'.$name.'` not found.',
                404
            );
        }
        if (!isset($this->routes[$key][$type]['handle'])) {
            excp(
                'Handler for route `'.$name.'` (`'.$type.'`) not found.'
            );
        }

        if ($middlewares = exists($this->routes[$key][$type], 'middlewares')) {
            return $this->mdwr($middlewares);
        }

        $this->handler   = $this->routes[$key][$type]['handle'];
        $this->routeVars = $this->assign($this->routes[$key][$type]['params']);

        return $this->execute();
    }

    protected function assign($params)
    {
        foreach (array_reverse($this->vars) as $key => $val) {
            $params[$key] = $val;
        }

        return $params;
    }

    protected function mdwr($middlewares)
    {
        $this->middlewares = $middlewares;
        ($this->middleware = WebFcty::make('middleware'))
        ->run($this, $middlewares);

        return $this;
    }

    public function fire()
    {
        ($this->request = WebFcty::make('request'))->run($this);

        return $this;
    }

    public function execute()
    {
        if (is_callable($this->handler)) {
            return response(($this->handler)());
        } elseif (is_string($this->handler)) {
            $args = explode('@', $this->handler);
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
            ], [$this, $act, $this->routeVars]);
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        return $this;
    }

    public function listen($name)
    {
        $handle = $this->listenHandleMap[$name];

        if (!exists($this->listenHandleMap, $name) ||
            !method_exists($this, $handle)
        ) {
            throw new \Lif\Core\Excp\MethodNotFound(__CLASS__, $handle);
        }

        return $this->$handle();
    }

    public function name()
    {
        return $this->name;
    }

    public function routes()
    {
        return $this->_route->routes;
    }

    public function middleware()
    {
        return $this->middleware;
    }

    public function middlewares()
    {
        return (
            is_object($this->middleware) &&
            method_exists($this->middleware, 'all')
        ) ? $this->middleware->all() : [];
    }

    public function argvs()
    {
        return $this->middleware->argvs;
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

    public function vars()
    {
        return $this->routeVars;
    }
}
