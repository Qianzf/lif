<?php

namespace Lif\Core\Excp;

class IllegalRouteDefinition extends \Exception
{
    protected $msg = [
        0 => 'Illegal route definition.',
        1 => 'Route handler must be Closure or String(`Controller`@`action`).',
        2 => 'String type of route handler must be formatted with `Controller@action.',
    ];
    public function __construct($err = 0, $format = 'json')
    {
        $this->message = $this->msg[$err] ?? $this->msg[0];

        exception($this, $format);
    }
}
