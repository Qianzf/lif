<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateUploadTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('upload', function ($table) {
            $table->pk('id');

            $table
            ->int('user')
            ->unsigned()
            ->comment('Who upload this file');

            $table
            ->string('filekey')
            ->comment('Uploaded file key in 3rd storage service');

            $table
            ->string('filename')
            ->nullable()
            ->comment('User custom uploaded filename');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true)
            ->comment('When upload this file');

            $table
            ->datetime('update_at')
            ->nullable()
            ->comment('When update this file');

            $table
            ->comment('User upload table');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('upload');
    }
}
