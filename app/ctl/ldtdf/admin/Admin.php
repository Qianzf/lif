<?php

namespace Lif\Ctl\Ldtdf\Admin;

class Admin extends Ctl
{
    public function index(\Lif\Mdl\User $user)
    {
        view('ldtdf/admin/index');
    }
}
