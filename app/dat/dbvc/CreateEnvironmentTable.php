<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateEnvironmentTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('environment', function ($table) {
            $table->pk('id');
            $table->string('type', 16);
            
            $table
            ->string('host', 128)
            ->comment('Outer-accessible address for this env');

            $table
            ->string('path')
            ->comment('Absolute path on server of this env');

            $table
            ->int('project')
            ->unsigned()
            ->default(0)
            ->comment('Project ID of this env bound to');
            
            $table
            ->int('server')
            ->unsigned()
            ->default(0)
            ->comment('Server ID of this env bound to');

            $table
            ->string('desc')
            ->nullable();
            
            $table
            ->string('status')
            ->default('running')
            ->comment('Status of this env => env_status.name');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('environment');
    }
}
