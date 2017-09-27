<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Trending;

class LDTDF extends Ctl
{
    public function index()
    {
        $entryRouteOfRole = '/'
        .$this->route
        .'/'
        .strtolower(share('__USER.role'));

        redirect($entryRouteOfRole);
    }

    public function trending(Trending $trending)
    {
        view('ldtdf/trending')->withTrending($trending->all());
    }
}
