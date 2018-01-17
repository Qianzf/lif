<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateProductTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('product', function ($table) {
            $table->pk('id', 'smallint', 3);
            $table->string('name');
            $table->text('desc');

            $table
            ->int('creator')
            ->unsigned();

            // $table
            // ->int('parent')
            // ->default(0)
            // ->unsigned();

            $table
            ->tinyint('order')
            ->unsigned()
            ->default(0);

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->comment('Product table (which is different with Project)');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('product');
    }
}
