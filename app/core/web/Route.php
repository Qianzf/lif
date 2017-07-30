<?php

namespace Lif\Core\Web;

use Lif\Core\Intf\Observable;
use Lif\Core\Abst\Container;

class Route extends Container implements Observable
{
    use \Lif\Core\Traits\Observable;
    
    protected $files = [];
    protected $observers = [];

    // Temporary Stacks
    private $prefixes    = [];
    private $middlewares = [];
    private $namespaces  = [];

    public $legalHttpMethods = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
        'HEAD',
    ];

    public function group(array $attrs, \Closure $closure)
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

        $closure();    // equal with `call_user_func($closure, $this)`

        $this->popAllTmpStack();     // Magic happen here
    }

    // This magic method is used to register web route only
    public function __call($name, $args)
    {
        $name = strtoupper($name);
        $this->denyNoneHttpMethods($name);
        $this->add($name, $args);
    }

    public function add($name, $args)
    {
        $route['name'] = $args[0];
        $this->parseRouteBindAndAlias($args, $route['bind'], $route['alias']);
        $this->fillRouteInfo($route);

        foreach ($this->observers as $observer) {
            $observer->onRegistered(
                'route',
                $name,
                $route
            );
        }

        return $this;
    }

    private function popAllTmpStack()
    {
        array_pop($this->prefixes);
        array_pop($this->middlewares);
        array_pop($this->namespaces);
    }

    public function parseRouteBindAndAlias($args, &$bind, &$alias)
    {
        $argCnt = count($args);
        if (2>$argCnt || 3<$argCnt) {
            excp('Wrong route definition.');
        }

        if (!is_string($args[0])) {
            excp('Illegal route name `'.$args[0].'`');
        }

        $defaultAlias = format_route_key(
            implode('/', $this->prefixes).
            '/'.
            $args[0]
        );
        if (2 === $argCnt) {
            if (!is_string($args[1]) &&
                !is_callable($args[1]) &&
                (!is_array($args[1]) || !isset($args[1]['bind']))
            ) {
                excp('Bad route definition.');
            }
            $bind = (
                is_array($args[1]) &&
                isset($args[1]['bind']) &&
                is_string($args[1]['bind']) &&
                $args[1]['bind']
            ) ? $args[1]['bind'] : $args[1];

            $alias = (
                is_array($args[1]) &&
                isset($args[1]['alias']) &&
                is_string($args[1]['alias']) &&
                $args[1]['alias']
            ) ? $args[1]['alias'] : $defaultAlias;
        } elseif (3 === $argCnt) {
            if (!is_array($args[1]) ||
                (
                    !is_string($args[2]) &&
                    !is_callable($args[2])
                )
            ) {
                excp('Bad route definition.');
            }
            $bind  = $args[1]['bind']  ?? $args[2];
            $alias = $args[1]['alias'] ?? $defaultAlias;
        }

        if (!legal_route_binding($bind)) {
            excp('Illegal route bind.');
        }
    }

    public function fillRouteInfo(&$route)
    {
        $prefix     = $this->prefixes ? implode('/', $this->prefixes) : '';
        $namespace  = $this->namespaces
        ? format_namespace($this->namespaces)
        : '';
        $route['middlewares'] = $this->middlewares;
        $route['namespace']   = $namespace;
        $route['name']        = format_route_key($prefix.'/'.$route['name']);
    }

    public function denyNoneHttpMethods($name)
    {
        if (!in_array($name, $this->legalHttpMethods)) {
            excp(
                'Illegal HTTP Method `'.$name.'`.'
            );
        }
    }

    protected function register($routes)
    {
        $routePath = pathOf('route');
        $web = $app = &$this;
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
        $this->addObserver($observer);
        $this->register($routes);
    }
}
