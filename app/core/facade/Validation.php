<?php

namespace Lif\Core\Facade;

use Lif\Core\Abst\Facade;

class Validation extends Facade
{
    // protected static $proxy = '\\Lif\\Core\\Validation';
    
    protected static function getProxy()
    {
        return nsOf('core', 'Validation');
    }
}
