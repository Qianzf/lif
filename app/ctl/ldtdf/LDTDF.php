<?php

namespace Lif\Ctl\Ldtdf;

class LDTDF extends Ctl
{
    public function index()
    {
        $entryRouteOfRole = format_route_key('/'
            .$this->route
            .'/'
            .strtolower(share('__USER.role'))
        );

        redirect(route($entryRouteOfRole));
    }
}
