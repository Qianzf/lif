<?php

namespace Lif\Mdwr\Auth;

class Tester
{
    public function handle($app)
    {
        if ('TESTER' !== strtoupper(share('__USER.role'))) {
            share_error_i18n('VIEW_PERMISSION_DENIED');
            
            session()->delete('__USER');

            return redirect('/dep/user/login');
        }
    }
}
