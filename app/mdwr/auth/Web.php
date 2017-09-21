<?php

namespace Lif\Mdwr\Auth;

use Lif\Core\Web\Session;
use Lif\Core\Abst\Container;

class Web extends Container
{
    protected $auth = false;

    public function handle($app)
    {
        if (($this->auth = session()->get('LOGGED_USER'))
            && ($this->auth['id'])
        ) {
            return $this->auth;
        }

        redirect(route('dep.user.login'));
    }
}
