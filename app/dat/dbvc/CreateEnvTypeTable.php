<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateEnvTypeTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('env_type', function ($table) {
            $table->pk('id');

            $table
            ->string('key')
            ->unique()
            ->comment('Environment type unique key');

            $table
            ->string('desc')
            ->comment('Environment type desc');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('env_type');
    }
}
