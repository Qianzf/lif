<?php

namespace Lif\Mdwr\Auth;

use Lif\Core\Web\Session;
use Lif\Core\Abst\Container;

class Web extends Container
{
    protected $auth = false;

    public function handle($app)
    {
        $s = new Session;

        if (! ($this->auth = $s->get('LOGGED_USER'))) {
            redirect(route('dep.user.login'));
        }

        return $this->auth;
    }
}
