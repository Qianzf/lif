<?php

namespace Lif\Core\Web;

use Lif\Core\Abst\Container;
use Lif\Core\Intf\Observable;

class Request extends Container implements Observable
{
    use \Lif\Core\Traits\Observable;
    
    protected $name    = 'request';
    protected $route   = null;
    protected $type    = null;
    protected $params  = null;
    protected $headers = null;

    public function __construct()
    {
        $this->init();
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
    }

    protected function init()
    {
        mb_http_input('UTF-8');
        
        return $this;
    }

    public function route()
    {
        if (!$this->route) {
            $uriArr = isset($_SERVER['REQUEST_URI'])
            ? parse_url($_SERVER['REQUEST_URI'])
            : false;

            $this->route = $uriArr['path'] ?? '/';
        }

        return $this->route;
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

    public function params()
    {
        if ($this->params) {
            return $this->params;
        }

        $cntType = isset($_SERVER['CONTENT_TYPE'])
        ? $_SERVER['CONTENT_TYPE']
        : 'application/x-www-form-urlencoded';

        $rawInput = file_get_contents('php://input');

        if (false !== mb_strpos($cntType, 'application/json')) {
            $params = json_decode($rawInput, true);
        } else {
            parse_str($rawInput, $params);
        }

        return $this->params = array_merge($params, $_REQUEST);
    }

    public function headers()
    {
        if ($this->headers) {
            return $this->headers;
        }

        return $this->headers = getallheaders();
    }

    public function run($observer, $params = [])
    {
        return $this
        ->addObserver($observer)
        ->trigger();
    }
}
