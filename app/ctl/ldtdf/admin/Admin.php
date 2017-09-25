<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\User as UserModel;

class Admin extends Ctl
{
    public function index(UserModel $user)
    {
        view('ldtdf/admin/index');
    }
}
