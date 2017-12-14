<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateRolePermissionTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('role_permission', function ($table) {
            $table->int('role')->unsigned();
            $table->int('permission')->unsigned();
        });
    }

    public function revert()
    {
        schema()->dropIfExists('role_permission');
    }
}
