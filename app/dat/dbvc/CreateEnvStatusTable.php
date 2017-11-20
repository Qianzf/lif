<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateEnvStatusTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('env_status', function ($table) {
            $table->pk('id');

            $table
            ->string('key')
            ->unique()
            ->comment('Environment status unique key');

            $table
            ->string('desc')
            ->comment('Environment status desc');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('env_status');
    }
}
