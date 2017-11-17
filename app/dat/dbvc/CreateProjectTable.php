<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateProjectTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('project', function ($table) {
            $table->pk('id');

            $table
            ->char('vcs', 16)
            ->default('git')
            ->comment('Project code used version controll system');

            $table
            ->string('url')
            ->comment('Project URL in vcs');

            $table->string('name');
            $table->tinytext('desc');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('project');
    }
}
