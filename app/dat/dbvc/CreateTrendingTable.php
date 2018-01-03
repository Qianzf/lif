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

            $table->string('action', 64);
            
            $table
            ->string('ref_type', 32)
            ->nullable()
            ->comment('Trending related object type');

            $table
            ->int('ref_id')
            ->unsigned()
            ->nullable()
            ->comment('Trending related object ID');

            $table
            ->string('ref_state')
            ->nullable()
            ->comment('Current state of this trending related object');

            $table
            ->int('target')
            ->unsigned()
            ->nullable()
            ->comment('User action target user: `user`.`id`');

            $table
            ->text('notes')
            ->nullable()
            ->comment('User action related notes');
            
            $table->comment('User Trending Table');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('trending');
    }
}
