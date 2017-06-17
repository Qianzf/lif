<?php

namespace Lif\Core\Interfaces;

interface Observer
{
    public function onRouteRegistered($name, $args);
}
