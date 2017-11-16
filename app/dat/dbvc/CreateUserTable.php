<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUserTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('user', function ($table) {
            $table->pk('id');
            $table->string('name');
            $table->string('account')->unique();
            $table->string('email')->unique();
            $table->string('passwd');
            $table->medint('role')->comment('User role ID');
            $table->enum('status', 0, 1)->default(1)->comment('User status ID');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('user');
    }
}
