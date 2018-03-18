<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateHttpapiEnvTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('httpapi_env', function ($table) {
            $table->pk('id');

            $table->int('project')->comment('httpapi_project.id');

            $table->char('scheme', 8)->default('http');

            $table->string('host');

            $table->smallint('port')->default(80);

            $table->string('prefix')->default('');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('httpapi_env');
    }
}
