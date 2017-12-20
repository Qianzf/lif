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
    protected $type    = null;
    protected $headers = null;
    protected $params  = [];
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
        return $this->params()->$key ?? null;
    }

    public function updateType()
    {
        if (('POST' == $this->type) &&
            !isset($_GET['__method']) &&
            in_array('__method', array_keys($this->params))
        ) {
            $forgeMethod = strtoupper($this->params['__method']);
            if (in_array($forgeMethod, [
                'PUT',
                'DELETE',
                'PATCH'
            ])) {
                $this->type = $forgeMethod;
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
        if (!$this->type) {
            $this->type = isset($_SERVER['REQUEST_METHOD'])
            ? $_SERVER['REQUEST_METHOD']
            : 'GET';
        }

        return $this->type;
    }

    public function all()
    {
        return $this->params()->toArray();
    }

    public function magic(string $key = null)
    {
        $this->params();

        return is_null($key)
        ? $this->magic
        : ($this->magic[$key] ?? null);
    }

    public function params($origin = null, bool $collect = true)
    {
        if ($this->params) {
            return $this->params;
        }

        $params  = $origin ?? $_REQUEST;
        $cntType = $_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded';

        if (false === mb_strpos($cntType, 'multipart/form-data')) {
            $rawInput = file_get_contents('php://input');

            if (false !== mb_strpos($cntType, 'application/json')) {
                $_params = (array) json_decode($rawInput, true);
            } elseif (false !== mb_strpos($cntType, 'application/xml')) {
                $_params = (array) xml2arr($rawInput);
            } else {
                parse_str($rawInput, $_params);
            }

            $params = array_merge($_params, $params);
        }

        array_walk($params, function (&$val, $key) use (&$params) {
            if ('__' === mb_substr($key, 0, 2)) {
                $this->magic[$key] = $val;
                unset($params[$key]);
            }
        });

        return $this->params = $collect ? collect($params) : $params;
    }

    public function gets()
    {
        $get = $_GET;
        
        array_walk($get, function (&$val, $key) use (&$get) {
            if ('__' === mb_substr($key, 0, 2)) {
                $this->magic[$key] = $val;
                unset($get[$key]);
            }
        });

        return $get;
    }

    public function posts()
    {
        if ($this->posts) {
            return $this->posts;
        }

        return $this->posts = $this->params($_POST, false);
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
