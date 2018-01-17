<?php

namespace Lif\Ctl\Ldtdf\PM;

class ProductManager extends \Lif\Ctl\Ldtdf\Ctl
{
    public function index()
    {   
        return view('ldtdf/pm/index')->share('hide-search-bar', true);
    }

    public function productsList()
    {
        return view('ldtdf/pm/products/list');
    }
}
