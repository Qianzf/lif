<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreatePermissionTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('permission', function ($table) {
            $table->pk('id');

            $table
            ->string('title')
            ->comment('Permission title');

            $table
            ->string('action', 64)
            ->comment('Standard http methods, case-insensitive');

            $table
            ->tinytext('route')
            ->comment('Http URL without parameters. support regex rules');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('permission');
    }
}
