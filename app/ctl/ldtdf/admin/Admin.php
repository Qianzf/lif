<?php

namespace Lif\Ctl\Ldtdf\Admin;

class Admin extends Ctl
{
    public function index(\Lif\Mdl\User $user)
    {

        // TODO
        // Model insert improving
        // Model detele
        $user->name = 'li';
        $user->email = '';
        dd($user->save());
        view('ldtdf/index');
    }
}
