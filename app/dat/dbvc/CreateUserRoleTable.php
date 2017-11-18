<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUserRoleTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('user_role', function ($table) {
            $table->pk('id');

            $table
            ->string('key', 64)
            ->comment('User role key');

            $table
            ->string('desc')
            ->comment('User role description');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('user_role');
    }
}
