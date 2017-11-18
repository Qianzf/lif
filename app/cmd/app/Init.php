<?php

namespace Lif\Cmd\App;

use Lif\Core\Abst\Command;

class Init extends Command
{
    protected $intro = 'Initialize LiF application';

    public function fire()
    {
        init_dit_table();
        init_job_table();
    }
}
