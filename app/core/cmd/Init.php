<?php

namespace Lif\Core\Cmd;

use Lif\Core\Abst\Command;

class Init extends Command
{
    protected $intro = 'Initialize LiF';

    public function fire()
    {
        init_dit_table();
        
        $this->initJob();
    }

    public function initJob()
    {
        schema()
        ->setAutocommit(false)
        ->createIfNotExists('__job__', function ($table) {
            $table->pk('id');
            $table->string('queue');
            $table->text('detail');
            
            $table
            ->tinyint('try')
            ->default(0)
            ->comment('How many tried times to be consider as failed');
            
            $table
            ->tinyint('tried')
            ->default(0)
            ->comment('Tried times of this job in current try loop');
            
            $table
            ->tinyint('retried')
            ->unsigned()
            ->comment('Failed times of this job')
            ->default(0);
            
            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->tinyint('timeout')
            ->unsigned()
            ->comment('The max execution time for this job');

            $table
            ->tinyint('restart')
            ->default(0)
            ->comment('Should this job need to be restarted');
            
            $table
            ->tinyint('lock')
            ->default(0)
            ->comment('Job running or not');

            $table->comment('Queue job table');
        })
        ->commit();
    }
}
