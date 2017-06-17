<?php

namespace Lif\Core\Traits;

trait Tol
{
    public function jsonResponse($err, $msg, $data = [], $alreadyJson = false)
    {
        header('Content-type:text/plain; charset=UTF-8');

        if ($alreadyJson) {
            exit($data);
        }

        exit(json_encode([
            'err'  => $err,
            'msg'  => $msg,
            'data' => $data,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
