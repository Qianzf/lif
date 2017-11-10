<?php

// -------------------------------
//     LiF middleware contract
// -------------------------------

namespace Lif\Core\Intf;

interface Middleware
{
    public function handle($app);
}
