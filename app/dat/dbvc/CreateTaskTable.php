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
            ->comment('User who created this task: `user`.`id`');

            $table
            ->string('title')
            ->comment('Task Title');

            $table
            ->string('status')
            ->comment('Task status => `task_status`.`key`');

            $table
            ->tinytext('url')
            ->comment('Task deatail page URL from outer system');

            $table
            ->char('custom', 8)
            ->default('no')
            ->comment('Custom task detail or not: yes/no');

            $table
            ->string('story_role')
            ->comment('User story 1st element: User Role');

            $table
            ->string('story_activity')
            ->comment('User story 2nd element: activity');

            $table
            ->string('story_value')
            ->comment('User story 3rd element: value');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('task');
    }
}
