<?php

namespace Lif\Mdwr\Auth;

class Admin
{
    public function handle($app)
    {
        if ('ADMIN' !== strtoupper(share('__USER.role'))) {
            share('__error', sysmsg('VIEW_PERMISSION_DENIED'));
            redirect('/dep/user/login');
        }
    }
}
