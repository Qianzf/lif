<?php

namespace Lif\Ctl\Ldtdf\Developer;

class Developer extends Ctl
{
    public function index()
    {
        share('hide-search-bar', true);
        
        view('ldtdf/developer/index');
    }
}
