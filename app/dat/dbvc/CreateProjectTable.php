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
            ->unique()
            ->comment('Project URL in vcs');

            $table->string('name');
            
            $table
            ->string('type')
            ->comment('Project type: client-side(APP) or server-side(WEB) or something else => `project_type`.`key`');

            $table
            ->tinytext('desc')
            ->nullable();

            $table
            ->string('token')
            ->nullable()
            ->comment('Integration with vcs APIs used token');

            $table
            ->char('script_type', 8)
            ->nullable()
            ->comment('Deploy type of this project');

            $table
            ->string('script_path')
            ->nullable()
            ->comment('Absolute path on server of this project deploy script');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('project');
    }
}
