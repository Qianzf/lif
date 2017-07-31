<?php

namespace Lif\Core\Intf;

interface Middleware
{
    public function handle($app);
}
