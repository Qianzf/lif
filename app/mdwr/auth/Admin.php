<?php

namespace Lif\Mdwr\Auth;

class Admin
{
    public function handle($app)
    {
        if ('ADMIN' !== strtoupper(share('__USER.role'))) {
            share_error_i18n('VIEW_PERMISSION_DENIED');
            
            session()->delete('__USER');

            return redirect('/dep/user/login');
        }
    }
}
