<?php

namespace Lif\Core\Cmd\Queue;

use Lif\Core\Abst\Command;

class Run extends Command
{
    use \Lif\Core\Traits\Queue;

    protected $intro = 'Start working on jobs for given queues on given connection';

    protected $option = [
        '-N'       => 'setQueues',
        '--name'   => 'setQueues',
        '-D'       => 'daemon',    // Overide global `-D` option
        '--daemon' => 'daemon',
        '-S'          => 'setStartSecs',
        '--startsecs' => 'setStartSecs',
        '--once'      => 'once',
        '-C'          => 'setQueueConn',
        '--conn'      => 'setQueueConn',
    ];

    protected $desc = [
        'daemon'       => 'Listen queue jobs background (default: true)',
        'setQueues'    => 'Specific queue names to listen',
        'setQueueConn' => 'Set queue connection for listening',
        'setStartSecs' => 'Set seconds for supervisor to decide whether worker is start success or not(default: 5)',
        'once'         => 'Run queue worker and exit after executed queue job once',
    ];

    // --------------------------------------------------------------
    //  If daemon is true then infinite looping queue jobs table
    //  And run queue jobs one by one without restarting framework
    //     
    //  However, some exceptions will still make daemon worker exit
    //  Eg: out of memory, fatal errors, etc
    //  
    //  Else if daemon is false then just looping given queue's jobs
    //  And exit queue worker when no jobs in given queues
    //     
    //  However, supervisor daemon will restart this queue worker
    //  In the long term, queue worker will performed like infinite
    //  looping
    //  
    //  The main difference between true and false is that in the 
    //  most common scnarios daemon is true will reduce framework
    //  rebooting times and save time and resources
    // -------------------------------------------------------------
    private $daemon    = true;
    // -------------------------------------------------------------

    // -------------------------------------------------------------
    //  The seconds for supervisor to decide whether program is
    //  running normally
    //  
    //  So it should be not less than supervisor program setting
    //  value `startsecs`
    // -------------------------------------------------------------
    private $startSecs = 5;
    // -------------------------------------------------------------

    public function fire()
    {
        // sleep($this->startSecs);

        while (true) {
            if (is_null($this->run())
                && !$this->daemon
            ) {
                exit(0);
            }

            usleep(42);
        }
    }

    protected function run()
    {
        if (! ($job = $this->getFirstJob())) {
            return null;
        }

        $success = true;
        try {
            $this->restartFailedJobs();
            $this->holdCurrentJob();

            // Run queue job
            $success = call_user_func([$job, 'run']);

            if ($success) {
                // Delete current job from queue
                $success = $this->outOfQueue();
            } else {
                $this->releaseCurrentJob();
            }
        } catch (\Exception $e) {
            exception($e);
        }

        return $success;
    }

    protected function once($value = null)
    {
        if (in_array($value, [
            '0',
            'false'
        ])) {
            return null;
        }

        $status = $this->run();

        if (is_null($status)) {
            exit($this->info('No queue jobs now.'));
        } elseif ($status) {
            $this->success(
                'One queue job has been executed successfully.'
            );
        } else {
            $this->fails(
                'One queue job execution failed.'
            );
        }
    }

    protected function daemon($value = null) : void
    {
        if (is_null($value)
            || ('1' === $value)
            || ('true' === $value)
        ) {
            $this->daemon = true;
        } elseif (('0' === $value)
            || 'false' === $value
        ) {
            $this->daemon = false;
        }
    }

    protected function setStartSecs(int $secs)
    {
        $this->startSecs = $secs;
    }
}
