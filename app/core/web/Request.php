<?php

namespace Lif\Core\Web;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observable;

class Request extends Container implements Observable
{
    use \Lif\Core\Traits\Observable;
    
    protected $name    = 'request';
    protected $route   = null;
    protected $url     = null;
    protected $method  = null;
    protected $headers = null;
    protected $params  = [];
    protected $gets    = [];
    protected $posts   = [];
    protected $magic   = [];

    public function __construct()
    {
        $this->init();
    }

    public function has(string $key)
    {
        $this->params();

        return isset($this->params[$key]);
    }

    public function get(string $key)
    {
        return $this->params($key);
    }

    public function updateType()
    {
        if (('POST' == $this->method) &&
            !isset($_GET['__method']) &&
            in_array('__method', array_keys($this->params))
        ) {
            $forgeMethod = strtoupper($this->params['__method']);
            if (in_array($forgeMethod, [
                'PUT',
                'DELETE',
                'PATCH'
            ])) {
                $this->method = $forgeMethod;
                unset($this->params['__method']);
            }
        }

        return $this;
    }

    protected function init()
    {
        mb_http_input('UTF-8');
        
        return $this;
    }

    public function route()
    {
        if (! $this->route) {
            $uriArr = isset($_SERVER['REQUEST_URI'])
            ? parse_url(implode('/', filter_route($_SERVER['REQUEST_URI'])))
            : false;

            $this->route = ($route = $uriArr['path'])
            ? '/'.$route
            : '/';
        }

        return $this->route;
    }

    public function url()
    {
        return $this->url = ($_SERVER['REQUEST_URI'] ?? '/');
    }

    public function type()
    {
        return $this->method();
    }

    public function method()
    {
        if (! $this->method) {
            $this->method = server('REQUEST_METHOD', 'GET');
        }

        return $this->method;
    }

    public function all()
    {
        return $this->params()->toArray();
    }

    public function magic(string $key = null)
    {
        $this->params();

        return is_null($key) ? $this->magic : ($this->magic[$key] ?? null);
    }

    public function params(string $key = null)
    {
        if (! $this->params) {
            $params = $this->parse($_REQUEST);

            $this->params = collect($params);
        }

        return $key ? $this->params->$key : $this->params;
    }

    public function parse(array $params = []) : array
    {
        $type = server('CONTENT_TYPE', 'application/x-www-form-urlencoded');

        if (false === mb_strpos($type, 'multipart/form-data')) {
            $stream = file_get_contents('php://input');

            if (false !== mb_strpos($type, 'application/json')) {
                $_params = (array) json_decode($stream, true);
            } elseif (false !== mb_strpos($type, 'application/xml')) {
                $_params = (array) xml2arr($stream);
            } else {
                parse_str($stream, $_params);
            }

            $params = array_merge($_params, $params);
        }

        array_walk($params, function (&$val, $key) use (&$params) {
            if ('__' === mb_substr($key, 0, 2)) {
                $this->magic[$key] = $val;
                unset($params[$key]);
            }
        });

        return $params;
    }

    public function gets(string $key = null)
    {
        if (! $this->gets) {
            $this->gets = $this->parse($_GET);
        }

        return $key ? ($this->gets[$key] ?? null) : $this->gets;
    }

    public function posts(string $key = null)
    {
        if (! $this->posts) {
            $this->posts = $this->parse($_POST);
        }

        return $key ? ($this->posts[$key] ?? null) : $this->posts;
    }

    public function getCleanPost(string $key)
    {
        $val = $this->posts($key);

        $this->unsetPost($key);

        return $val;
    }

    public function unsetPost(string $key)
    {
        $this->posts();

        if ($this->posts[$key] ?? false) {
            unset($this->posts[$key]);
        }

        return $this;
    }

    public function setPost(string $key, $value)
    {
        $this->posts();
        
        $this->posts[$key] = $value;

        return $this;
    }

    public function unset(string $key = null)
    {
        if ($key && isset($this->params[$key])) {
            unset($this->params[$key]);
        }
    }

    public function headers()
    {
        if ($this->headers) {
            return $this->headers;
        }

        return $this->headers = collect(getallheaders());
    }

    public function run($observer, $params = [])
    {
        $this
        ->addObserver($observer)
        ->done();

        return $this;
    }
}
