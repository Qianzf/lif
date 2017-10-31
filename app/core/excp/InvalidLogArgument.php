<?php

namespace Lif\Core\Excp;

class InvalidLogArgument extends \Exception
{
    private $level = [
        1 => 'debug',
        2 => 'info',
        3 => 'notice',
        4 => 'warning',
        5 => 'error',
        6 => 'critical',
        7 => 'alert',
        8 => 'emergency',
    ];
}
