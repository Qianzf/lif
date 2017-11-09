<?php

namespace Lif\Mdwr\Auth;

class Developer
{
    public function handle($app)
    {
        if ('DEVELOPER' !== strtoupper(share('user.role'))) {
            share_error_i18n('VIEW_PERMISSION_DENIED');
            
            session()->delete('user');

            return redirect('/dep/user/login');
        }
    }
}
