<?php

namespace Lif\Ctl\Ldtdf\Developer;

class Developer extends Ctl
{
    public function index()
    {
        share('hidden-search-bar', true);
        
        view('ldtdf/developer/index');
    }
}
