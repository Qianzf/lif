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
        $querys = $this->request->gets();

        legal_or($querys, [
            'id'       => ['int|min:1', null],
            'search'   => ['string', null],
            'sort'     => ['ciin:asc,desc', 'desc'],
            'page'     => ['int|min:1', 1],
        ]);

        $pageScale = 16;
        $page      = $querys['page'];
        
        if ($id = ($querys['id'] ?? false)) {
            $products = $product->whereId($id)->get();
        } else {
            if ($search = ($querys['search'] ?? false)) {
                $product->whereName('like', "%{$search}%");
            }

            $products = $product
            ->sort([
                'create_at' => $querys['sort']
            ])
            ->limit(($page-1)*$pageScale, $pageScale)
            ->get();
        }

        $records  = $product->count();
        $pages    = ceil($records / $pageScale);

        return view('ldtdf/pm/products/index')
        ->withProductsPagesRecords(
            $products,
            $pages,
            $records
        );
    }
}
