<?php

// ---------------------------
//     LiF Web Application
// ---------------------------

namespace Lif\Core\Strategy;

use Lif\Core\Intf\{Observer, Strategy};
use Lif\Core\Abst\{Container, Factory};

class Web extends Container implements Observer, Strategy
{
    protected $name        = 'web';
    protected $_route      = null;
    protected $request     = null;
    protected $middleware  = [];
    protected $response    = null;
    protected $globalMiddlewares  = [];

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

        if (file_exists(pathOf('app', '.lock'))) {
            abort(423, sysmsg('SERVICE_IS_STOPPED'));
        }
    }

    // Load web helpers
    protected function load(): Web
    {
        load(
            pathOf('_aux').'web.php',
            'Web helper file'
        );

        return $this;
    }

    public function withMiddlewares(...$globalMiddlewares): Web
    {
        if ((1 === count($globalMiddlewares))
            && isset($globalMiddlewares[0])
            && is_array($globalMiddlewares[0])
        ) {
            $globalMiddlewares = $globalMiddlewares[0];
        }

        $this->globalMiddlewares = $globalMiddlewares;

        return $this;
    }

    public function withRoutes(...$routes): Web
    {
        if ((1 === count($routes))
            && isset($routes[0])
            && is_array($routes[0])
        ) {
            $routes = $routes[0];
        }

        if ($routes) {
            $this->_route = Factory::make('route', nsOf('web'));
            $this->_route->run($this, $routes);
        }

        return $this;
    }

    // escape route key for variables-bound scenario
    // !!! Must `format_route_key($key)` before `escape()`
    protected function escape($key)
    {
        if (! isset($this->routes[$key])) {
            $arr     = explode('.', $key);
            $subsets = subsets(array_keys($arr));
            
            foreach ($subsets as &$val) {
                $tmp  = $arr;
                $vars = [];
                foreach ($val as &$_val) {
                    $tmp[$_val] = '{?}';
                    $vars[] = $arr[$_val];
                }
                $escapedKey = implode('.', $tmp);

                if (isset($this->routes[$escapedKey])) {
                    $this->vars = array_reverse($vars);
                    unset($val, $_val, $tmp);
                    return $escapedKey;
                }
            }

            unset($val, $_val, $tmp);

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

        if (! in_array($type, array_keys($this->routes[$key]))) {
            client_error(
                "`{$type}` for route `{$name}` not found.",
                404
            );
        }
        if (! isset($this->routes[$key][$type]['handle'])) {
            excp(
                "Handler for route `{$name}` (`{$type}`) not found."
            );
        }

        $route = $this->routes[$key][$type];
        // Filter route parameters
        if ($needFilter = ($route['filters'] ?? false)) {
            $this->filter($needFilter, $route['params']);
        }

        if ($needUnset = ($route['blacklist'] ?? false)) {
            $this->unset($needUnset);
        }

        // !!! Hander must set before middlewares be executed
        $this->handler   = $route['handle'];
        $this->routeVars = $this->assign($route['params']);
        $this->passingMiddlewares(
            exists($route, 'middlewares')
        );

        return $this;
    }

    protected function assign($params)
    {
        foreach (array_reverse($this->vars) as $key => $val) {
            $params[$key] = $val;
        }

        return $params;
    }

    protected function filter($filters, $params) : Web
    {
        if (count($params) != count($this->vars)) {
            excp('Illegal route params count.');
        }

        $_params = array_combine($params, $this->vars);

        if (true !== ($err = validate($_params, $filters))) {
            client_error('Route parameter validation failed: '.$err);
        }

        return $this;
    }

    protected function unset($blacklist)
    {
        foreach ($this->globalMiddlewares as $idx => $middleware) {
            if (in_array($middleware, $blacklist)) {
                unset($this->globalMiddlewares[$idx]);
            }
        }

        return $this;
    }

    protected function passingMiddlewares(array $middlewares = [])
    {
        // !!! Make sure global middlewares be executed first
        ($this->middleware = Factory::make('middleware', nsOf('web')))
        ->run($this, array_merge(
            $this->globalMiddlewares,
            $middlewares
        ));

        return $this;
    }

    protected function callbackMiddlewares()
    {
        foreach ($this->middlewares as $key => $middleware) {
            if ($middleware instanceof \Lif\Core\Abst\Middleware) {
                call_user_func([$middleware, 'callback'], $this);
            }
        }

        return $this;
    }

    public function fire()
    {
        ($this->request = Factory::make('request', nsOf('web')))->run($this);

        return $this;
    }

    public function execute()
    {
        if (is_callable($this->handler)) {
            $response = $this->__callableSafe($this->handler, $this->vars);
        } elseif (is_string($this->handler)) {
            $args = explode('@', $this->handler);
            if ((count($args) !== 2)
                || !($ctlName = trim(format_ns($args[0])))
                || !($act = trim($args[1]))
            ) {
                throw new \Lif\Core\Excp\IllegalRouteDefinition(2);
            }

            $ctl = Factory::make($ns = nsOf('ctl', $ctlName));
            $act = lcfirst($act);

            if (method_exists($ctl, '__lif__')) {
                $response = call_user_func_array([
                    $ctl,
                    '__lif__'
                ], [$this, $act, $this->routeVars]);
            } else {
                if (! method_exists($ctl, $act)) {
                    excp("Method not found: `{$ns}@{$act}`");
                }

                $response = call_user_func_array(
                    [$ctl, $act],
                    $this->routeVars
                );
            }
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        if ($response instanceof \Lif\Core\Web\Response) {
            $this->response = $response;
        }

        // Execute registered middleware callbacks
        $this->callbackMiddlewares();

        return $this;
    }

    public function listen($name)
    {
        if (!($handle = exists($this->listenHandleMap, $name))
            || !method_exists($this, $handle)
        ) {
            throw new \Lif\Core\Excp\MethodNotFound(__CLASS__, $handle);
        }

        $this->$handle();
        
        return $this;
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
            is_object($this->middleware)
            && method_exists($this->middleware, 'all')
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

    public function url()
    {
        return $this->request->url;
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
