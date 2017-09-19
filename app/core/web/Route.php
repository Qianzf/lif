<?php

// -----------------------------
//     LiF routes management
// -----------------------------

namespace Lif\Core\Web;

use Lif\Core\Intf\Observable;
use Lif\Core\Abst\Container;

class Route extends Container implements Observable
{
    use \Lif\Core\Traits\Observable;
    
    protected $name      = 'route';
    protected $files     = [];
    protected $routes    = [];      // all routes and their bindings
    protected $aliases   = [];      // all routes and their aliases

    // Temporary stacks for route
    private $prefixes     = [];
    private $middlewares  = [];
    private $namespaces   = [];
    private $_prefixes    = [];
    private $_middlewares = [];
    private $_namespaces  = [];
    private $type         = null;    // Current single route type
    private $route        = null;    // Current single route name
    private $groupDepth   = 0;       // Nest group routes depth

    protected function any(...$args): Route
    {
        if (($attrsIf = exists($args, 1)) && exists($attrsIf, 'alias')) {
            excp('Route alias not supported when use `any` or `match`');
        }

        foreach (legal_http_methods() as $method) {
            $this
            ->deny($method)
            ->add($method, $args);
        }

        return $this;
    }

    protected function match($methods, ...$args): Route
    {
        if (($attrsIf = exists($args, 1)) && exists($attrsIf, 'alias')) {
            excp('Route alias not supported when use `any` or `match`');
        }

        foreach (array_unique(array_filter($methods)) as $method) {
            $this
            ->deny($method)
            ->add($method, $args);
        }

        return $this;
    }

    public function group(array $attrs, \Closure $closure): Route
    {
        ++$this->groupDepth;
        $this->pushCurrentGroup($attrs);
        $closure();      // equal with `call_user_func($closure, $this)`
        $this->popCurrentGroup();    // Magic happen here
        --$this->groupDepth;

        return $this;
    }

    // Push current group route's attrs into tmp stacks
    private function pushCurrentGroup($attrs): Route
    {
        $this->push(
            $attrs,
            $this->prefixes,
            $this->middlewares,
            $this->namespaces
        );

        return $this;
    }

    private function push(
        $attrs,
        &$prefixes,
        &$middlewares,
        &$namespaces
    ): Route {
        // If current route hasn't prefix/middlewares/namespace
        // We need a stub for current route anyway

        $prefix = $namespace = false;

        if ($prefix = exists($attrs, 'prefix')) {
            if (! is_string($prefix)) {
                excp('Route prefix type is not string.');
            }
        }

        $prefixes[] = $prefix;

        if ($middleware = exists($attrs, 'middleware')) {
            $string = is_string($middleware);
            $array  = is_array($middleware);
            if (!$string && !$array) {
                excp(
                    'Route middlewares type neither string nor array.'
                );
            }
            if ($string && !in_array($middleware, $middlewares)) {
                $middleware = [$middleware];
            }
        } else {
            $middleware = [];
        }

        if ($middleware) {
            $middlewares = array_unique(array_merge(
                $middlewares,
                $middleware
            ));
        }

        if ($namespace = exists($attrs, 'namespace')) {
            if (!is_string($namespace)) {
                excp('Route namespace type is not string.');
            }
        }

        $namespaces[] = $namespace;

        return $this;
    }

    // Push current single non-group route's attrs into tmp stacks
    private function pushCurrentOne($attrs): Route
    {
        $this->push(
            $attrs,
            $this->_prefixes,
            $this->_middlewares,
            $this->_namespaces
        );

        return $this;
    }

    // This magic method is used to register web route only
    // @$name String => route type
    // @$args Array  => route attrs
    public function __call($name, $args): Route
    {
        $this
        ->deny($name)
        ->add($name, $args);

        return $this;
    }

    public function add($type, $args): Route
    {
        $this
        ->parse($args, $route)
        ->join($route)
        ->register(strtoupper($type), $route)
        // !!! Reset temp stack after one route is registered
        ->popCurrentOne();

        return $this;
    }

    private function popCurrentOne(): Route
    {
        array_pop($this->_prefixes);
        array_pop($this->_middlewares);
        array_pop($this->_namespaces);

        return $this;
    }

    private function popCurrentGroup(): Route
    {
        array_pop($this->prefixes);
        array_pop($this->middlewares);
        array_pop($this->namespaces);

        return $this;
    }

    // Cancel some middlewares for current single route
    public function cancel(...$middlewares): Route
    {
        if ((1 == count($middlewares))
            && isset($middlewares[0])
            && is_array($middlewares[0])
        ) {
            $middlewares = $middlewares[0];
        }
        
        if ($middlewares) {
            $tmpRoutes = $this->routes[$this->route][$this->type]['middlewares'];
            foreach ($tmpRoutes as $idx => $middleware) {
                if (in_array($middleware, $middlewares)) {
                    unset($tmpRoutes[$idx]);
                }
            }

            $this->routes[$this->route][$this->type]['middlewares'] = $tmpRoutes;
        }

        return $this;
    }

    // parse route definition and save basic attrs
    protected function parse($args, &$route): Route
    {
        $argCnt = count($args);

        if (2>$argCnt || 3<$argCnt) {
            excp('Wrong route definition.');
        }

        if (!is_string($args[0])) {
            excp('Illegal route name `'.$args[0].'`');
        }

        $alias = false;
        if (2 === $argCnt) {
            if (!is_string($args[1]) &&
                !is_callable($args[1]) &&
                (!is_array($args[1]) || !isset($args[1]['bind']))
            ) {
                excp('Bad route definition.');
            }

            if (is_array($args[1]) && ($attrs = $args[1])) {
                $bind = (
                    exists($attrs, 'bind') && is_string($attrs['bind'])
                ) ? $attrs['bind'] : $attrs;
                $alias = (
                    exists($attrs, 'alias') && is_string($attrs['alias'])
                ) ? $attrs['alias'] : false;

                $this->pushCurrentOne($attrs);
            } else {
                $bind = $args[1];
            }
        } elseif (3 === $argCnt) {
            if (!is_array($args[1]) || !$args[2] || (
                    !is_string($args[2]) && !is_callable($args[2])
                )
            ) {
                excp('Bad route definition.');
            }

            if ($attrs = $args[1]) {
                $bind  = exists($attrs, 'bind') ? $attrs['bind'] : $args[2];
                $alias = exists($attrs, 'alias');
                $this->pushCurrentOne($attrs);
            } else {
                $bind = $args[2];
            }
        }

        if (! legal_route_binding($bind)) {
            excp('Illegal route binding.');
        }

        $route['name']  = $args[0];
        $route['bind']  = $bind;
        $route['alias'] = $alias;

        return $this;
    }

    // extract variables from route name
    protected function extract($name)
    {
        preg_match_all('/\{(\w+)\}/u', $name, $matches);

        return $matches[1] ?? [];
    }

    // join route basic attrs with tmp attrs in stack
    protected function join(&$route): Route
    {
        $prefix = $this->prefixes ? implode('/', $this->prefixes) : '/';
        
        if (is_callable($route['bind'])) {
            $handle = $route['bind'];
        } elseif (is_string($route['bind'])) {
            $handle = format_namespace($this->namespaces).'\\'.$route['bind'];
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        $route['middlewares'] = $this->middlewares;
        $route['handle']      = $handle;
        $route['name']        = format_route_key($prefix.'/'.$route['name']);
        unset($route['bind']);

        return $this;
    }

    // deny non-http methods
    public function deny(&$name): Route
    {
        if (!is_string($name)) {
            excp('Illegal HTTP method format.');
        }

        if (!in_array(strtoupper($name), legal_http_methods())) {
            excp('Illegal HTTP method `'.$name.'`.');
        }

        return $this;
    }

    // register and save this route
    protected function register($type, $route): Route
    {
        $this->type = $type;
        $rawName    = $route['name'];
        $name       = $this->route = escape_route_name($rawName);

        if (isset($this->routes[$name][$type])) {
            excp(
                'Duplicate definition on `'.
                get_raw_route($name).
                '` of `'.$type.'`.'
            );
        }
        if (in_array($route['alias'], array_keys($this->aliases))) {
            excp(
                'Duplicate route alias `'.
                $route['alias'].
                '` for `'.
                get_raw_route($name).
                '`, already set for route `'.
                get_raw_route($this->aliases[$route['alias']]).'`.'
            );
        }

        $alias = ($route['alias'] ? $route['alias'] : $name);
        $aliasArr = [
            'route' => $alias,
            'type'  => $type,
        ];
        $this->aliases[$alias] = $aliasArr;
        $this->routes[$name][$type] = [
            'handle'      => $route['handle'],
            'params'      => $this->extract($rawName),
            'alias'       => $aliasArr,
            'middlewares' => $route['middlewares'],
        ];

        return $this;
    }

    // load route defination files
    protected function load($routes): Route
    {
        $routePath = pathOf('route');
        foreach ($routes as $route) {
            $path = $routePath.$route.'.php';
            $file = pathinfo($path);
            if (!is_file($path)
                || !isset($file['extension'])
                || !('php' === $file['extension'])
            ) {
                excp(
                    'Missing or illegal route file `'.$route.'.php`.'
                );
            }

            $this->files[] = $path;
            include_once $path;
        }

        return $this;
    }

    protected function share(): Route
    {
        $GLOBALS['LIF_ROUTES']         = $this->routes;
        $GLOBALS['LIF_ROUTES_ALIASES'] = $this->aliases;
        
        return $this;
    }

    public function files() 
    {
        return $this->files;
    }

    public function aliases()
    {
        return $this->aliases;
    }

    public function run($observer, $routes = [])
    {
        $this
        ->addObserver($observer)
        ->load($routes)
        ->share()
        ->done();

        return $this;
    }
}
