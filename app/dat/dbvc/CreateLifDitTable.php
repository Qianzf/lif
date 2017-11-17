<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateLifDitTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('lif_dit', function ($table) {
            $table->pk('id');
            $table->string('dit')->unique();
            $table->tinyint('version')->default(1);

            $table
            ->timestamp('create_at')
            ->default('CURRENT_TIMESTAMP()', true);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('lif_dit');
    }
}
