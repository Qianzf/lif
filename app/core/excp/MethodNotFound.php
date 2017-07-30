<?php

namespace Lif\Core\Excp;

class MethodNotFound extends \Exception
{
    public function __construct($class, $method, $format = 'json')
    {
        $this->message = 'Method `'
        .$method
        .'()` of `'
        .$class
        .'` not exists.';

        exception($this, $format);
    }
}
