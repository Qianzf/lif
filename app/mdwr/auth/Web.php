<?php

namespace Lif\Mdwr\Auth;

class Web
{
    protected $auth = false;

    public function handle($app)
    {
        if (($this->auth = share('user'))
            && (false !== exists($this->auth, 'id'))
            && (false !== exists($this->auth, 'role'))
        ) {
            $timestamp = time();
            // Update new session if 'remember me' is checked
            if (($remember = share('remember'))
                && (($remember + 60) < $timestamp)
            ) {
                share('remember', $timestamp);
                session()->update();
            }

            return $this->auth;
        }

        redirect('/dep/user/login');
    }
}
