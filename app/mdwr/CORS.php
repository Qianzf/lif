<?php

// Global CORS control
// @cjli

namespace Lif\Mdwr;

class CORS extends Mdwr
{
    public function __construct()
    {
    }

    public function handle($app)
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
    }
}
