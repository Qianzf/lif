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
    protected $observers = [];

    // Temporary stacks for route
    private $prefixes    = [];
    private $middlewares = [];
    private $namespaces  = [];

    public function group(array $attrs, \Closure $closure)
    {
        $this->pushTmpRouteStacks($attrs);
        $closure();    // equal with `call_user_func($closure, $this)`
        $this->popAllTmpStacks();     // Magic happen here
    }

    // Push current route's 3 type of attrs into tmp stacks:
    public function pushTmpRouteStacks($attrs)
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
        $this->denyNoneHttpMethods($name);
        $this->add($name, $args);

        return $this;
    }

    public function add($type, $args)
    {
        return $this
        ->parseRouteAttrs($args, $route)
        ->fillRouteInfo($route)
        ->register($type, $route);
    }

    private function popAllTmpStacks()
    {
        array_pop($this->prefixes);
        array_pop($this->middlewares);
        array_pop($this->namespaces);

        return $this;
    }

    public function parseRouteAttrs($args, &$route)
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

                $this->pushTmpRouteStacks($attrs);
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
                $bind  = exists($attrs, 'bind');
                $alias = exists($attrs, 'alias');
                $this->pushTmpRouteStacks($attrs);
            }

            $bind = (false === $bind) ? $args[2] : $bind;
        }

        if (!legal_route_binding($bind)) {
            excp('Illegal route bind.');
        }

        $route['name']  = $args[0];
        $route['bind']  = $bind;
        $route['alias'] = $alias;

        return $this;
    }

    public function fillRouteInfo(&$route)
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
        $route['name']        = format_route_key($prefix.$route['name']);

        if (!$route['alias']) {
            $route['alias'] = $route['name'];
        }

        return $this;
    }

    public function denyNoneHttpMethods($name)
    {
        if (!in_array($name, legal_http_methods())) {
            excp(
                'Illegal HTTP Method `'.$name.'`.'
            );
        }

        return $this;
    }

    protected function register($type, $route)
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
        return $this->app->aliases();
    }

    public function run($observer, $routes)
    {
        return $this
        ->addObserver($observer)
        ->load($routes)
        ->trigger();
    }
}