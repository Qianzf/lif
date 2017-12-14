<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateServerTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('server', function ($table) {
            $table->pk('id');
            $table->string('name');

            $table
            ->char('location', 8)
            ->default('remote')
            ->comment('Location relative to inner system: local/remote');
            
            $table
            ->string('host', 128)
            ->comment('Server Host: IP/Domain');

            $table
            ->medint('port')
            ->default(22)
            ->comment('Server Port');
            
            $table
            ->string('user', 64)
            ->comment('User of server');
            
            $table
            ->string('pubk')
            ->nullable()
            ->comment('Public key file path');
            
            $table
            ->string('prik')
            ->nullable()
            ->comment('Private key file path');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('server');
    }
}
