<?php

namespace Lif\Core;

use Lif\Core\interfaces\Observer;

class Application implements Observer
{
    use \Lif\Core\Traits\Tol;

    public $route = '/';
    public $reqType = 'GET';
    public $_routes = [];
    public $params = [];

    public function __construct()
    {
        $this->init();
        $this->route   = $this->getRoute();
        $this->reqType = $this->getReqType();
        $this->params  = $this->getParamsFromRawInput();
        Route::addObserver($this);
        Route::register();
    }

    public function init()
    {
        mb_internal_encoding('UTF-8');
        mb_http_input('UTF-8');
        mb_language('uni');
        mb_regex_encoding('UTF-8');
        mb_http_output('UTF-8');
        ob_start('mb_output_handler');
    }

    public function onRouteRegistered($name, $args)
    {
        $routeKey    = $this->formatRouteKey($args[0]);
        $routeType   = $name;
        $routeHandle = $args[1];

        $this->_routes[$routeKey][$routeType] = $routeHandle;
    }

    public function getParamsFromRawInput()
    {
        if ($this->reqType === 'GET') {
            $params = $_GET;
        } else {
            parse_str(file_get_contents('php://input'), $params);
        }
        return $params;
    }

    public function formatRouteKey($route)
    {
        return implode('_', array_filter(explode('/', $route)));
    }

    public function getRoute()
    {
        return isset($_SERVER['PATH_INFO'])
        ? urldecode($_SERVER['PATH_INFO'])
        : '/';
    }

    public function getReqType()
    {
        return isset($_SERVER['REQUEST_METHOD'])
        ? $_SERVER['REQUEST_METHOD']
        : 'CLI';
    }

    public function handle()
    {

        $routeKey = $this->formatRouteKey($this->route);
        if (!isset($this->_routes[$routeKey])) {
            $this->jsonResponse(404, 'route `'.$this->route.'` not found.');
        }
        if (!in_array($this->reqType, array_keys($this->_routes[$routeKey]))) {
            $this->jsonResponse(404, '`'.$this->reqType.'` for route `'.$this->route.'` not found.');
        }

        $handler = $this->_routes[$routeKey][$this->reqType];
        if (is_callable($handler)) {
            $this->jsonResponse(null, null, $handler(), true);
        }
        if (is_string($handler)) {
            $args = explode('@', $handler);
            if (count($args) !== 2 || !($ctl = trim($args[0])) || !($act = trim($args[1]))) {
                $this->jsonResponse(415, 'String type of route handler must be formatted with `Controller@action`)', [
                    'source' => "`Route::{$this->reqType}('{$this->route}', '{$handler}')`"
                ]);
            }
            $ctlName = 'Lif\Ctl\\'.ucfirst($ctl);
            $actName = lcfirst($act);

            (new $ctlName())->$actName();
        } else {
            $this->jsonResponse(415, 'route handler must be Closure or String(`Controller@action`)');
        }
    }
}
