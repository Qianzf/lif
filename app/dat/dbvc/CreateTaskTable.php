<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateTaskTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('task', function ($table) {
            $table->pk('id');

            $table
            ->int('creator')
            ->unsigned()
            ->comment('User ID who created this task');

            $table
            ->string('status')
            ->comment('Task status => `task_status`.`key`');

            $table
            ->tinytext('url')
            ->comment('Task deatail page URL from outer system');

            $table
            ->string('title')
            ->comment('Task Title');

            $table
            ->tinyint('custom')
            ->default(0)
            ->comment('Custom task detail or not: 0 => no; 1 => yes');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('task');
    }
}
