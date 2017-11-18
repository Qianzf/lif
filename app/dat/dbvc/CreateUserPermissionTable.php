<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUserPermissionTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('user_permission', function ($table) {
            $table->pk('id');

            $table
            ->string('key')
            ->comment('User permission key');

            $table
            ->string('desc')
            ->comment('User permission description');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('user_permission');
    }
}
