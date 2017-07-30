<?php

namespace Lif\Core\Intf;

interface Observer
{
    public function onRegistered($name, $key = null, $args = null);
    public function nameAsObserver();
}
