<?php

namespace Lif\Ctl\Ldtdf;

class LDTDF extends Ctl
{
    public function index()
    {
        $entryRouteOfRole = '/dep/'.strtolower(share('__USER.role'));

        redirect($entryRouteOfRole);
    }
}
