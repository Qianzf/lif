<?php

namespace Lif\Mdwr\Auth;

class Jwt
{
    use \Lif\Traits\SimpleJWT;

    protected $auth = false;

    public function __construct()
    {
    }

    public function handle($app)
    {
        if (false === ($this->auth = $this->authorise($app->headers))) {
            client_error('Unauthorized', 401);
        }

        return $this->auth;
    }
}
