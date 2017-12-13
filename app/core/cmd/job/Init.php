<?php

namespace Lif\Core\Cmd\Job;

use Lif\Core\Abst\Command;

class Init extends Command
{
    protected $intro = 'Init LiF queue job';

    protected $option = [
        '-F'      => 'force',
        '--force' => 'force',
    ];

    protected $desc = [
        'force' => 'Force init queue job ignore previous data',
    ];

    protected $force = false;

    public function fire()
    {
        if (schema()->hasTable('__job__')) {
            if ($this->force) {
                db()->truncate('__job__');
            }
        } else {
            init_job_table();
        }
    }

    public function force()
    {
        $this->force = true;
    }
}
