<?php

namespace Lif\Core\Excp;

class API extends \Exception
{
    public function __construct($msg, $err = 500)
    {
        $this->message = $msg;
        $this->code    = $err;

        exception($this);
    }
}
