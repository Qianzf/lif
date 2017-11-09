<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\User;

class Admin extends Ctl
{
    public function index(User $user)
    {
        share('hidden-search-bar', true);
        
        view('ldtdf/admin/index');
    }
}
