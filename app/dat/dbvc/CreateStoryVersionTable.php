<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateStoryVersionTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('story_version', function ($table) {
            $table->pk('id', 'bigint');

            $table
            ->int('story')
            ->unsigned()
            ->comment('Related story ID: `story`.`id`');

            $table
            ->string('title')
            ->comment('User story Title');

            $table
            ->string('role')
            ->comment('User story 1st element: User Role');

            $table
            ->string('activity')
            ->comment('User story 2nd element: Activity');

            $table
            ->string('value')
            ->comment('User story 3rd element: Value');

            $table
            ->text('extra')
            ->nullable()
            ->comment('Extra data of this story');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table->comment('Story version log');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('story_version');
    }
}
