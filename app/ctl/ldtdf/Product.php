<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{Product as ProductModel};

class Product extends Ctl
{
    public function info(ProductModel $product)
    {
        return view('ldtdf/pm/products/info')
        ->withProduct($product)
        ->share('hide-search-bar', true);
    }
}
