<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUserGroupMapTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('user_group_map', function ($table) {
            $table->pk('id');

            $table
            ->int('user')
            ->unsigned()
            ->comment('`user`.`id`');

            $table
            ->tinyint('group')
            ->unsigned()
            ->comment('`user_group`.`id`');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('user_group_map');
    }
}
