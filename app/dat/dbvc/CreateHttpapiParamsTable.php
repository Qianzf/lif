<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateHttpapiParamsTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('httpapi_params', function ($table) {
            $table->pk('id');
            
            $table->char('origin', 16)->comment('env;header;query;body');

            $table->string('content_type', 64);

            $table->int('user')->comment('user.id');

            $table->string('k');

            $table->text('v');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('httpapi_params');
    }
}
