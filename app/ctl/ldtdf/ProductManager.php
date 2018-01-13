<?php

namespace Lif\Ctl\Ldtdf;

class ProductManager extends Ctl
{
    public function index()
    {
        share('hide-search-bar', true);
        
        view('ldtdf/pm/index');
    }
}
