<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateHttpapiProjectTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('httpapi_project', function ($table) {
            $table->pk('id');

            $table->string('name');
            $table->text('desc');

            $table
            ->int('creator')
            ->unsigned();

            $table
            ->char('visibility', 16)
            ->default('world');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('httpapi_project');
    }
}
