<?php

// ---------------------------------------------------------
//     Each LiF queue worker will listen on the queue
//     named related to that worker's name
//     
//     All LiF Queue workers will listen on `restart`
//     flag in queue jobs table
//     
//     Restart command is used to retry failed jobs
//     (`tried` >= `try`) by update given queues table's
//     `restart` to 1 (default queue is `default`)
//     
//     If flag is 1 then that worker will do 3 things for
//     the queue it is related:
//     - reset `restart` flag to 0
//     - reset `tried` to 0
//     - increase `retried` times 
// ---------------------------------------------------------

namespace Lif\Core\Cmd\Queue;

use Lif\Core\Abst\Command;

class Restart extends Command
{
    use \Lif\Core\Traits\Queue;

    protected $intro = 'Restart LiF queue workers by given names';

    protected $option = [
        '-N'     => 'setQueues',
        '--name' => 'setQueues',
        '-C'     => 'setQueueConn',
        '--conn' => 'setQueueConn',
    ];

    protected $desc = [
        'setQueues'    => 'Specific queue names to be restarted (Separated by `,` without space: `--name=a,b,c`, `-N a,b,c`)',
        'setQueueConn' => 'Set queue connection for restating',
    ];

    public function fire()
    {
        $this->prepare();
        
        $queues = $this->queues ? implode(', ', $this->queues) : '*All*';
        $msg    = 'Queue workers (all or part of: '.$queues.')';
        if ($this->getQueue()->setRestart($this->queues)) {
            $this->success(
                $msg
                .' have been restarted successfully.'
            );
        } else {
            $this->fails(
                $msg
                .' were failed in restarting.'
            );
        }
    }
}
