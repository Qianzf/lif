<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateTrendingTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('trending', function ($table) {
            $table->pk('id');
            $table->datetime('at');
            
            $table
            ->int('user')
            ->unsigned()
            ->comment('User ID');

            $table->string('action', 32);
            
            $table
            ->string('ref_type', 32)
            ->nullable();

            $table
            ->int('ref_id')
            ->unsigned()
            ->nullable()
            ->comment('Trending related object ID');
            
            $table->comment('User Trending Table');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('trending');
    }
}
