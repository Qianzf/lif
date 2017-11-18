<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateEventTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('event', function ($table) {
            $table->pk('id');
            
            $table
            ->string('key', 64)
            ->comment('System event key');

            $table
            ->string('desc')
            ->comment('System event description');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('event');
    }
}
