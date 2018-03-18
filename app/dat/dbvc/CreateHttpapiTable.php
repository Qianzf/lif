<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateHttpapiTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('httpapi', function ($table) {
            $table->pk('id');
            
            $table->int('project')->comment('httpapi_project.id');
            $table->int('cate')->default(0)->comment('httpapi_cate.id');

            $table->string('name');
            $table->string('path');
            $table->char('method', 16);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('httpapi');
    }
}
