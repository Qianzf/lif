<?php

namespace Lif\Core\Excp;

class InvalidLogArgument extends \Exception
{
    public function __construct(string $level)
    {
        $this->message = 'Log level not exists: '.$level;
    }
}
