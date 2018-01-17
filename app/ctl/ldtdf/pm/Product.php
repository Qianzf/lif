<?php

namespace Lif\Ctl\Ldtdf\PM;

use Lif\Mdl\{Product as ProductModel, User};

class Product extends \Lif\Ctl\Ldtdf\Ctl
{
    public function update(ProductModel $product)
    {
        return $this->responseOnUpdated($product);
    }

    public function create(ProductModel $product)
    {
        $user = share('user.id');

        return $this->responseOnCreated(
            $product,
            lrn('pm/products'),
            function () use ($user) {
                if (! ci_equal(model(User::class, $user)->role, 'pm')) {
                    return 'CREATE_PERMISSION_DENIED';
                }

                $this->request->setPost('creator', $user);
            }
        );
    }

    public function edit(ProductModel $product)
    {
        return view('ldtdf/pm/products/edit')
        ->withProductEditable($product, true)
        ->share('hide-search-bar', true);
    }

    public function index(ProductModel $product)
    {
        return view('ldtdf/pm/products/index')
        ->withProducts($product->all());
    }
}
