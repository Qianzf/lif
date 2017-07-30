<?php

namespace Lif\Core\Traits;

trait WebGetter
{
    public function routes()
    {
        return $this->routes;
    }

    public function aliases()
    {
        return $this->aliases;
    }

    public function route()
    {
        return $this->route;
    }

    public function params()
    {
        return $this->params;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function _route()
    {
        return $this->_route;
    }

    public function request()
    {
        return $this->request;
    }
}
