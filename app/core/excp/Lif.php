<?php

// -------------------------------------------------
//     This is the default exception file of Lif
//     Which is styled output in JSON format
// -------------------------------------------------

namespace Lif\Core\Excp;

class Lif extends \Exception
{
    public function __construct($msg, $err, $format = 'json')
    {
        $this->message = $msg;
        $this->code    = $err;
        
        exception($this, $format);
    }
}
