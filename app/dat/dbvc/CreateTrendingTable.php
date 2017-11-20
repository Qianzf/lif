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
            ->int('uid')
            ->unsigned()
            ->comment('User ID');
            
            $table
            ->int('tid')
            ->unsigned()
            ->nullable()
            ->comment('Task ID');

            $table
            ->string('event', 64)
            ->comment('Event key => `event`.`key`');

            $table
            ->text('detail')
            ->nullable()
            ->comment('Trending detail');
            
            $table->comment('User Trending Table');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('trending');
    }
}