<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateHttpapiCateTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('httpapi_cate', function ($table) {
            $table->pk('id');
            
            $table->int('project')->unsigned()->comment('httpapi_project.id');

            $table->string('name', 32);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('httpapi_cate');
    }
}
