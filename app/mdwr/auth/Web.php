<?php

namespace Lif\Mdwr\Auth;

class Web extends \Lif\Core\Abst\Middleware
{
    protected $auth = false;

    public function passing($app)
    {   
        if (($this->auth = share('user'))
            && (false !== exists($this->auth, 'id'))
            && (false !== exists($this->auth, 'role'))
        ) {
            $timestamp = time();
            // Update new session if `remember me` is checked
            if (($remember = share('remember'))
                && (($remember + 60) < $timestamp)
            ) {
                share('remember', $timestamp);
                session()->update();
            }

            return $this->auth;
        }

        share('redirect_url', $app->url());

        redirect(lrn('users/login'));
    }
}
