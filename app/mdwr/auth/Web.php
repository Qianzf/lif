<?php

namespace Lif\Mdwr\Auth;

class Web
{
    protected $auth = false;

    public function handle($app)
    {
        if (($this->auth = session()->get('__USER'))
            && (false !== exists($this->auth, 'id'))
            && (false !== exists($this->auth, 'role'))
        ) {
            return $this->auth;
        }

        redirect('/dep/user/login');
    }
}
