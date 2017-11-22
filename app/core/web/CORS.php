<?php

// -----------------------------------------------
//     Web browser cross origin resource share
// -----------------------------------------------

namespace Lif\Core\Web;

class CORS extends \Lif\Core\Abst\Middleware
{
    public function passing($app)
    {
        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Max-Age'       => 86400,
            'Access-Control-Allow-Headers' => implode(',', [
                'Access-Control-Allow-Origin',
                'AUTHORIZATION',
            ]),
        ];

        if ('OPTIONS' == $app->request->type()) {
            response();
        }

        // Set headers
        if (! headers_sent()) {
            foreach ($headers as $key => $val) {
                header($key.': '.$val);
            }
        }

        return true;
    }

    public function callback($app)
    {
    }
}
