<?php

namespace Lif\Mdwr\Auth;

class Jwt extends \Lif\Core\Abst\Middleware
{
    use \Lif\Traits\SimpleJWT;

    protected $auth = false;

    public function passing($app)
    {
        if (false === ($this->auth = $this->authorise($app->headers))) {
            client_error('Unauthorized', 401);
        }

        return $this->auth;
    }
}
