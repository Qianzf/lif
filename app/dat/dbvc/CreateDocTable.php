<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateDocTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('doc', function ($table) {
            $table->pk('id');

            $table->string('title');
            $table->text('content');

            $table
            ->int('creator')
            ->unsigned();

            $table
            ->int('folder')
            ->default(0)
            ->unsigned();

            $table
            ->tinyint('order')
            ->unsigned()
            ->default(0);

            $table
            ->char('visibility', 16)
            ->default('world');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->datetime('update_at')
            ->default('CURRENT_TIMESTAMP()', true);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('doc');
    }
}
