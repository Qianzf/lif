<?php

namespace Lif\Mdwr;

class Auth extends Mdwr
{
    public function handle($app)
    {
        if (!exists($app->headers, 'AUTHORIZATION')) {
            client_error('Unauthorized', 401);
        }

        return $app->headers['AUTHORIZATION'];
    }
}
