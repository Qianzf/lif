<?php

namespace Lif\Core\Intf;

interface Observer
{
    public function listen($name);
    public function name();
}
