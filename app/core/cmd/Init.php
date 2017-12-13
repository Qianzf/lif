<?php

namespace Lif\Core\Cmd;

use Lif\Core\Abst\Command;

class Init extends Command
{
    protected $intro = 'Initialize LiF';

    public function fire()
    {
        init_dit_table();
        init_job_table();
    }
}
