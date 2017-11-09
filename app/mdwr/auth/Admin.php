<?php

namespace Lif\Mdwr\Auth;

class Admin
{
    public function handle($app)
    {
        if ('ADMIN' !== strtoupper(share('user.role'))) {
            share_error_i18n('VIEW_PERMISSION_DENIED');
            
            session()->delete('user');

            return redirect('/dep/user/login');
        }
    }
}
