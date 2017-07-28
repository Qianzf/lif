<?php

namespace Lif\Core;

use Lif\Core\Intf\Observer;
use Lif\Core\DB;

class App implements Observer
{
    public $route     = '/';
    public $reqType = 'GET';
    public $_routes = [];
    public $params  = [];
    public $pdo     = null;

    public function __construct()
    {
        $this->init();
        $this->route   = $this->getRoute();
        $this->reqType = $this->getReqType();
        $this->params  = $this->getReqParams();

        // if (isset($this->config['env']) && ('local' == $this->config['env'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        // }

        Route::run($this);
    }

    public function __get($name)
    {
        if ('db' === strtolower($name)) {
            if (!$this->pdo) {
                $this->pdo = (new DB($this->config['pdo']))->pdo;
            }
            return $this->pdo;
        }
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

    public function onRegistered($name, $key, $args)
    {
        if ('route' === $name) {
            $routeKey    = $this->formatRouteKey($args[0]);
            $routeType   = $key;
            $routeHandle = $args[1];

            $this->_routes[$routeKey][$routeType] = $routeHandle;
        }
    }

    public function getReqParams()
    {
        $cntType = isset($_SERVER['CONTENT_TYPE'])
        ? $_SERVER['CONTENT_TYPE']
        : 'application/x-www-form-urlencoded';

        $rawInput = file_get_contents('php://input');

        if (false !== mb_strpos($cntType, 'application/json')) {
            $params = json_decode($rawInput, true);
        } else {
            parse_str($rawInput, $params);
        }

        return array_merge($params, $_REQUEST);
    }

    public function formatRouteKey($route)
    {
        $routeKey = implode('_', array_filter(explode('/', $route)));
        return $routeKey;
    }

    public function getRoute()
    {
        $uriArr = isset($_SERVER['REQUEST_URI'])
        ? parse_url($_SERVER['REQUEST_URI'])
        : false;
        return $uriArr['path'] ?? $this->apiPrefix;
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
            error(404, 'route `'.$this->route.'` not found.');
        }
        if (!in_array($this->reqType, array_keys($this->_routes[$routeKey]))) {
            error(404, '`'.$this->reqType.'` for route `'.$this->route.'` not found.');
        }

        $handler = $this->_routes[$routeKey][$this->reqType];
        if (is_callable($handler)) {
            response($handler());
        }
        
        if (is_string($handler)) {
            $args = explode('@', $handler);
            if (count($args) !== 2 || !($ctl = trim($args[0])) || !($act = trim($args[1]))) {
                error(415, 'String type of route handler must be formatted with `Controller@action`)', [
                    'source' => "`Route::{$this->reqType}('{$this->route}', '{$handler}')`"
                ]);
            }
            $ctlName = 'Lif\Ctl\\'.ucfirst($ctl);
            $actName = lcfirst($act);

            return (new $ctlName($this))->$actName();
        } else {
            error(415, 'route handler must be Closure or String(`Controller@action`)');
        }
    }
}
