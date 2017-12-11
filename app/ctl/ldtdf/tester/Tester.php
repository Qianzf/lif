<?php

namespace Lif\Ctl\Ldtdf\Tester;

class Tester extends \Lif\Ctl\Ldtdf\Ctl
{
    public function index()
    {
        share('hide-search-bar', true);
        
        view('ldtdf/tester/index');
    }
}
