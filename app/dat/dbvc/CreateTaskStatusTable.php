<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateTaskStatusTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('task_status', function ($table) {
            $table->pk('id', 'tinyint');

            $table
            ->string('key')
            ->unique()
            ->nullable()
            ->comment('Task status unique key');

            $table
            ->string('val')
            ->comment('Task status text desc');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('task_status');
    }
}
