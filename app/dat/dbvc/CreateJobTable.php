<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateJobTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('job', function ($table) {
            $table->pk('id');
            $table->string('queue');
            $table->text('detail');
            $table->tinyint('try')->default(0);
            $table->tinyint('tried')->default(0);
            $table->tinyint('retried')->default(0);
            
            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->tinyint('timeout')
            ->comment('The max execution time for this job');

            $table
            ->enum('restart', 0, 1)
            ->default(0)
            ->comment('Should this job need to be restarted');
            
            $table
            ->enum('lock', 0, 1)
            ->default(0)
            ->comment('Job running or not');

            $table->comment('Queue job table');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('job');
    }
}
