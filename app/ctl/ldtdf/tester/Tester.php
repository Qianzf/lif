<?php

namespace Lif\Ctl\Ldtdf\Tester;

class Tester extends Ctl
{
    public function index()
    {
        share('hide-search-bar', true);
        
        view('ldtdf/tester/index');
    }
}
