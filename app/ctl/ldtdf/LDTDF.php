<?php

namespace Lif\Ctl\Ldtdf;

class LDTDF extends Ctl
{
    public function index()
    {
        $uid   = share('LOGGED_USER.id');

        view('ldtdf/index')->withUid($uid);
    }
}
