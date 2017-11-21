<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUserGroupTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('user_group', function ($table) {
            $table->pk('id', 'tinyint');

            $table
            ->string('name', 64)
            ->unique();
            
            $table
            ->tinytext('desc')
            ->nullable();
        });
    }

    public function revert()
    {
        schema()->dropIfExists('user_group');
    }
}
