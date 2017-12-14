<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUserPermissionTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('user_permission', function ($table) {
            $table->int('user')->unsigned();
            $table->int('permission')->unsigned();
        });
    }

    public function revert()
    {
        schema()->dropIfExists('user_permission');
    }
}
