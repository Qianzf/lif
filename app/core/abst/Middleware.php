<?php

// ----------------------------------
//     LiF middleware abstraction
// ----------------------------------

namespace Lif\Core\Abst;

abstract class Middleware
{
    // Execute before controller and after request
    public function passing($app)
    {
    }

    // Execute before PHP exit and after controller
    public function callback($app)
    {
    }
}
