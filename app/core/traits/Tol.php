<?php

namespace Lif\Core\Traits;

trait Tol
{
    public function response($dat = [], $msg = 'ok', $err = 200, $format = 'json')
    {
        response($dat, $msg, $err, $format);
    }

    public function error($err, $msg)
    {
    	response([], $msg, $err, 'json');
    }
}
