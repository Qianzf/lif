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
    protected $cache     = false;
    protected $updated   = false;   // routes updated or not compared to last registering
    protected $_routes   = null;    // routes cache file
    protected $_liases   = null;    // routes aliases cache file
    protected $files     = [];
    protected $routes    = [];      // all routes and their bindings
    protected $aliases   = [];      // all routes and their aliases

    // Temporary stacks for route
    private $prefixes     = [];
    private $middlewares  = [];
    private $namespaces   = [];
    private $filters      = [];
    private $ctl          = [];
    private $_prefixes    = [];
    private $_middlewares = [];
    private $_namespaces  = [];
    private $_filters     = [];
    private $_ctl         = [];
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
    private function pushCurrentGroup(array $attrs): Route
    {
        $this->push(
            $attrs,
            $this->prefixes,
            $this->middlewares,
            $this->namespaces,
            $this->filters,
            $this->ctl
        );

        return $this;
    }

    private function push(
        $attrs,
        &$prefixes,
        &$middlewares,
        &$namespaces,
        &$filters,
        &$ctl
    ): Route {
        // If current route hasn't prefix/middlewares/namespace/filters/ctl
        // We need a stub for current route anyway

        $prefix = $namespace = false;

        if ($prefix = exists($attrs, 'prefix')) {
            if (! is_string($prefix)) {
                excp('Route prefix type is not string.');
            }
        }

        $prefixes[] = $prefix;

        if ($namespace = exists($attrs, 'namespace')) {
            if (!is_string($namespace)) {
                excp('Route namespace type is not string.');
            }
        }

        $namespaces[] = $namespace;

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

        $middlewares[] = $middleware;

        if ($filter = exists($attrs, 'filter')) {
            if (! is_array($filter)) {
                excp('Route filter must be an array.');
            }
        } else {
            $filter = [];
        }

        $filters[] = $filter;

        if ($controller = exists($attrs, 'ctl')) {
            if (! is_string($controller)) {
                excp('Controller name must be a string.');
            }
        } else {
            $controller = null;
        }

        $ctl[] = $controller;

        return $this;
    }

    // Push current single non-group route's attrs into tmp stacks
    private function pushCurrentOne($attrs): Route
    {
        $this->push(
            $attrs,
            $this->_prefixes,
            $this->_middlewares,
            $this->_namespaces,
            $this->_filters,
            $this->_ctl
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
        array_pop($this->_filters);
        array_pop($this->_ctl);

        return $this;
    }

    private function popCurrentGroup(): Route
    {
        array_pop($this->prefixes);
        array_pop($this->middlewares);
        array_pop($this->namespaces);
        array_pop($this->filters);
        array_pop($this->ctl);

        return $this;
    }

    private function resetStack() : void
    {
        $this->prefixes     = [];
        $this->middlewares  = [];
        $this->namespaces   = [];
        $this->filters      = [];
        $this->ctl          = [];
        $this->_prefixes    = [];
        $this->_middlewares = [];
        $this->_namespaces  = [];
        $this->_filters     = [];
        $this->_ctl         = [];
        $this->type         = null;
        $this->route        = null;
        $this->groupDepth   = 0;
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

            $this->routes
            [$this->route][$this->type]['middlewares'] = $tmpRoutes;
        }

        return $this;
    }

    // parse route definition and save basic attrs
    protected function parse($args, &$route): Route
    {
        $argCnt = count($args);

        if (1>$argCnt || 3<$argCnt) {
            excp('Wrong route definition.');
        } elseif ((1 == $argCnt) && !is_array($args[0])) {
            excp('Wrong route definition.');
        } elseif ((1 < $argCnt) && !is_string($args[0])) {
            excp('Illegal route name `'.$args[0].'`');
        }

        $alias = false;
        if (1 == $argCnt) {
            if (!($name = exists($args[0], 'name'))
                || !($bind = exists($args[0], 'bind'))
            ) {
                excp('Missing route name and handler.');
            }

            $alias = exists($args[0], 'as');
            $attrs = $args[0];
            unset($attrs['name'], $attrs['as']);
            $args[0] = $name;
            $this->pushCurrentOne($attrs);
        } elseif (2 === $argCnt) {
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
        $prefix  = $this->prefixes  ? implode('/', $this->prefixes)  : '/';
        $prefix .= $this->_prefixes ? implode('/', $this->_prefixes) : '';
        
        $namespaces = [];
        if ($this->namespaces || $this->_namespaces) {
            $namespaces = array_merge(
                $this->namespaces,
                $this->_namespaces
            );
        }

        $middlewares = [];
        if ($this->middlewares || $this->_middlewares) {
            $middlewares = array_merge(
                $this->middlewares,
                $this->_middlewares
            );
        }

        $filters = [];
        if ($this->filters || $this->_filters) {
            $filters = array_merge(
                $this->filters,
                $this->_filters
            );
        }

        if (is_callable($route['bind'])) {
            $handle = $route['bind'];
        } elseif (is_string($route['bind'])) {
            $_handle = $route['bind'];
            // Here always use the latest item of tmp stack
            // Single route controller can replace group controller
            $ctl = end($this->_ctl) ?: end($this->ctl) ?: null;

            if ($ctl) {
                // check action name legality when controller is given
                if (! preg_match('/^\w+$/u', $route['bind'])) {
                    excp(
                        'Illegal action name when controller was given: '
                        .$route['bind']
                    );
                }

                $_handle = $ctl.'@'.$route['bind'];
            }

            $handle = format_namespace($namespaces).'\\'.$_handle;
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        $route['middlewares'] = $middlewares;
        $route['filters']     = $filters;
        $route['bind']        = $handle;
        $route['name']        = format_route_key($prefix.'/'.$route['name']);

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

        $alias = $route['alias']
        ? $route['alias']
        : $this->type.':'.$name;

        if (in_array($alias, array_keys($this->aliases))) {
            excp(
                'Duplicate route alias `'.
                $alias.
                '` for `'.
                get_raw_route($name).
                '`, already set for route `'.
                get_raw_route($alias).'`.'
            );
        }

        $aliasArr = [
            'route' => $route['name'],
            'type'  => $type,
        ];
        $this->aliases[$alias] = $aliasArr;
        $this->routes[$name][$type] = [
            'handle'      => $route['bind'],
            'params'      => $this->extract($rawName),
            'alias'       => $aliasArr,
            'middlewares' => array_values_oned($route['middlewares']),
            'filters'     => array_values_oned($route['filters']),
        ];

        return $this;
    }

    // load route defination files
    protected function load($routes): Route
    {
        $this->cache = config('app.route.cache') ?? false;

        if ($this->cache) {            
            $this->_routes  = pathOf('cache', 'route/routes.json');
            $this->_aliases = pathOf('cache').'route/aliases.json';
        
            if (file_exists($this->_routes) && file_exists($this->_aliases)) {
                $routes  = json_decode(
                    file_get_contents($this->_routes),
                    true
                );
                $aliases = json_decode(
                    file_get_contents($this->_aliases),
                    true
                );

                if ($routes && $aliases) {
                    $this->routes  = $routes;
                    $this->aliases = $aliases;

                    return $this;
                }
            }
        }

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

            $this->resetStack();
        }

        $this->updated = true;

        return $this;
    }

    protected function share(): Route
    {
        if ($this->cache && $this->updated) {
            if (!$this->_routes || !$this->_aliases) {
                excp('Routes cache path unreachable.');
            }

            if ($this->routes && $this->aliases) {
                // arr2code($this->routes, pathOf('cache', 'route/routes.php'))
                // arr2code($this->aliases, pathOf('cache', 'route/aliases.php'))
                file_put_contents($this->_routes, json_encode(
                    $this->routes
                ));

                file_put_contents($this->_aliases, json_encode(
                    $this->aliases
                ));
            }
        }

        $GLOBALS['LIF_ROUTES']         = $this->routes;
        $GLOBALS['LIF_ROUTES_ALIASES'] = $this->aliases;
        
        return $this;
    }

    public function filter(...$args) : Route
    {
        if (! $args) {
            excp('No filter object and rules.');
        } elseif ((count($args) != 1) || !isset($args[0])) {
            excp('Filter rules must be an array only.');
        } elseif (! isset($this->routes[$this->route][$this->type])) {
            excp('Route not found.');
        }

        $filters = $this->routes[$this->route][$this->type]['filters'];

        $this->routes[$this->route][$this->type]['filters'] = array_unique(array_merge(
            $filters,
            $args[0]
        ));

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

    public function current()
    {
        if (! $this->app
            || !is_object($this->app)
            || !method_exists($this->app, 'route')
        ) {
            return null;
        }

        return $this->app->route();
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
