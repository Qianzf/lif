<?php

namespace Lif\Ctl\Ldtdf;

class LDTDF extends Ctl
{
    public function index()
    {
        $entryRouteOfRole = '/dep/'.strtolower(share('user.role'));

        redirect($entryRouteOfRole);
    }
}
