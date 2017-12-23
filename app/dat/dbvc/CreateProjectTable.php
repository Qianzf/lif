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
            ->string('build_script')
            ->nullable()
            ->comment('Build script, absolute path of project (executable)');

            $table
            ->string('config_api')
            ->comment('Project config read/write interface (executable)');

            $table
            ->char('config_order', 8)
            ->default('after')
            ->comment('Config API execute order compare to build script: `before` or `after`');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('project');
    }
}
