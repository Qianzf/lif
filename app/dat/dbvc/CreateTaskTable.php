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
            ->comment('User who created this task => `user`.`id`');

            $table
            ->int('story')
            ->unsigned()
            ->nullable()
            ->comment('User story ID this task relate to => `story`.`id`');

            $table
            ->int('project')
            ->unsigned()
            ->comment('Project ID this task relate to => `project`.`id`');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->string('status')
            ->comment('Task status => `task_status`.`key`');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('task');
    }
}
