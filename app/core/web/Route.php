<?php

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
    private $prefixes    = [];
    private $middlewares = [];
    private $namespaces  = [];

    public function group(array $attrs, \Closure $closure)
    {
        $this->push($attrs);
        $closure();      // equal with `call_user_func($closure, $this)`
        $this->pop();    // Magic happen here
    }

    // Push current route's attrs into tmp stacks:
    public function push($attrs)
    {
        if ($prefix = exists($attrs, 'prefix')) {
            if (!is_string($prefix)) {
                excp('Route prefix type is not string.');
            }
            $this->prefixes[] = $prefix;
        }

        if ($middlewares = exists($attrs, 'middleware')) {
            $string = is_string($middlewares);
            $array  = is_array($middlewares);
            if (!$string && !$array) {
                excp(
                    'Route middlewares type neither string nor array.'
                );
            }
            if ($string && !in_array($middlewares, $this->middlewares)) {
                $this->middlewares[] = $middlewares;
            } elseif ($array) {
                $this->middlewares = array_unique(array_merge(
                    $this->middlewares,
                    $middlewares
                ));
            }
        }

        if ($namespace = exists($attrs, 'namespace')) {
            if (!is_string($namespace)) {
                excp('Route namespace type is not string.');
            }
            $this->namespaces[] = $namespace;
        }

        return $this;
    }

    // This magic method is used to register web route only
    // @$name String => route type
    // @$args Array  => route attrs
    public function __call($name, $args)
    {
        $name = strtoupper($name);
        $this->deny($name);
        $this->add($name, $args);

        return $this;
    }

    public function add($type, $args)
    {
        return $this
        ->parse($args, $route)
        ->join($route)
        ->register($type, $route);
    }

    private function pop()
    {
        array_pop($this->prefixes);
        array_pop($this->middlewares);
        array_pop($this->namespaces);

        return $this;
    }

    // parse route definition and save basic attrs
    protected function parse($args, &$route)
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

                $this->push($attrs);
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
                $this->push($attrs);
            } else {
                $bind = $args[2];
            }
        }

        if (!legal_route_binding($bind)) {
            excp('Illegal route binding.');
        }

        $route['name']   = $args[0];
        $route['bind']   = $bind;
        $route['alias']  = $alias;

        return $this;
    }

    // extract variables from route name
    protected function extract($name)
    {
        preg_match_all('/\{(\w+)\}/u', $name, $matches);

        return $matches[1] ?? [];
    }

    // join route basic attrs with tmp attrs in stack
    protected function join(&$route)
    {
        $prefix = $this->prefixes ? implode('/', $this->prefixes) : '/';
        
        if (is_callable($route['bind'])) {
            $handle = $route['bind'];
        } elseif (is_string($route['bind'])) {
            $handle = format_namespace($this->namespaces).$route['bind'];
        } else {
            throw new \Lif\Core\Excp\IllegalRouteDefinition(1);
        }

        $route['middlewares'] = $this->middlewares;
        $route['handle']      = $handle;
        $route['name']        = format_route_key($prefix.'/'.$route['name']);

        return $this;
    }

    // deny non-http methods
    public function deny($name)
    {
        if (!in_array($name, legal_http_methods())) {
            excp(
                'Illegal HTTP Method `'.$name.'`.'
            );
        }

        return $this;
    }

    // register and save this route
    protected function register($type, $route)
    {
        $rawName = $route['name'];
        $name    = escape_route_name($rawName);
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
    protected function load($routes)
    {
        $routePath = pathOf('route');
        foreach ($routes as $route) {
            $path = $routePath.$route.'.php';
            $file = pathinfo($path);
            if (!is_file($path) ||
                !isset($file['extension']) ||
                !('php' === $file['extension'])
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
        return $this
        ->addObserver($observer)
        ->load($routes)
        ->trigger();
    }
}
