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
        
        prepare_user_role_data();
        prepare_task_status_data();
        prepare_event_data();
    }
}
