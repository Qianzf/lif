<?php

namespace Lif\Core\Interfaces;

interface Observer
{
    public function onRegistered($name, $args);
}
