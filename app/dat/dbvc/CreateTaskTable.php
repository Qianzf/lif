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
            ->comment('User story ID this task relate to => `story`.`id`');

            $table
            ->int('project')
            ->unsigned()
            ->comment('Project ID this task relate to => `project`.`id`');

            $table
            ->string('status')
            ->comment('Task status => `task_status`.`key`');

            $table
            ->string('branch')
            ->nullable()
            ->comment('Task related code branch in vcs');

            $table
            ->char('manually', 8)
            ->default('no')
            ->comment('Whether task deployment needs manually help: yes/no');

            $table
            ->int('current')
            ->unsigned()
            ->nullable()
            ->comment('Current realted user of this task => `user`.`id`');

            $table
            ->text('notes')
            ->nullable()
            ->comment('Notes of this task');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('task');
    }
}
