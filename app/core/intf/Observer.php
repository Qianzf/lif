<?php

namespace Lif\Core\Intf;

interface Observer
{
    public function onRegistered($name, $key, $args);
}
