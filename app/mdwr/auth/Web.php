<?php

namespace Lif\Mdwr\Auth;

class Web
{
    protected $auth = false;

    public function handle($app)
    {
        if (($this->auth = share('__USER'))
            && (false !== exists($this->auth, 'id'))
            && (false !== exists($this->auth, 'role'))
        ) {
            $timestamp = time();
            // Update new session if 'remember me' is checked
            if (($remember = share('__remember'))
                && (($remember + 60) < $timestamp)
            ) {
                share('__remember', $timestamp);
                session()->update();
            }

            return $this->auth;
        }

        redirect('/dep/user/login');
    }
}
