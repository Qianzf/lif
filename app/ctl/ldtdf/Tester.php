<?php

namespace Lif\Ctl\Ldtdf;

class Tester extends Ctl
{
    public function index()
    {
        share('hide-search-bar', true);
        
        view('ldtdf/tester/index');
    }
}
