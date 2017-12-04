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
            ->char('origin_type', 8)
            ->default('story')
            ->comment('Task origin type: story/bug');

            $table
            ->int('origin_id')
            ->unsigned()
            ->comment('Task origin id');

            $table
            ->int('creator')
            ->unsigned()
            ->comment('User who created this task => `user`.`id`');

            $table
            ->int('project')
            ->unsigned()
            ->comment('Project ID this task relate to => `project`.`id`');

            $table
            ->text('notes')
            ->nullable()
            ->comment('Notes of this task creation');

            $table
            ->string('status')
            ->comment('Task status => `task_status`.`key`');

            $table
            ->int('current')
            ->unsigned()
            ->nullable()
            ->comment('Current realted user of this task => `user`.`id`');

            $table
            ->string('branch', 64)
            ->nullable()
            ->comment('Task related code branch in vcs');

            $table
            ->string('env', 64)
            ->nullable()
            ->comment('Deployed enviornment of current task');

            $table
            ->char('manually', 8)
            ->default('no')
            ->comment('Whether task deployment needs manually help: yes/no');

            $table
            ->text('deploy')
            ->nullable()
            ->comment('Notes of this task deployment, when `manually`=yes');

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
