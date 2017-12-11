<?php

namespace Lif\Core\Cmd\Queue;

use Lif\Core\Abst\Command;
use Lif\Core\Cli\Timeout;

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
    private $daemon = true;
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
    
    private $timer = null;

    public function fire()
    {
        $this->prepare();
        
        // sleep($this->startSecs);

        while (true) {
            sleep(1);
            if (is_null($this->run()) && !$this->daemon) {
                exit(0);
            }
        }
    }

    protected function run()
    {
        if (! ($job = $this->getFirstJob())) {
            return null;
        }

        try {
            $this->restartFailedJobs();
            $this->holdCurrentJob();

            if (0 < ($timeout = $this->getJobTimeout())) {
                if (! fe('pcntl_fork')) {
                    excp('Install PCNTL first.');
                }

                // Create share memory and signal
                if (! defined('JOB_STATUS')) {
                    define('JOB_STATUS', 0);
                }

                $shm     = shm_attach(ftok(__FILE__, 'm'));
                $signal  = sem_get(ftok(__FILE__, 's'));
                $success = false;
                $pid     = pcntl_fork();
                $expire  = time()+$timeout;

                if (-1 === $pid) {
                    excp('Fork failed.');
                }

                if (0 === $pid) {
                    // Do queue job in child process
                    $status = call_user_func([$job, 'run']);
                    @sem_acquire($signal);
                    shm_put_var($shm, JOB_STATUS, intval($status));
                    @sem_release($signal);
                    @sem_remove($signal);
                    // Exit child process and let master process return result
                    exit(posix_kill(posix_getpid(), SIGKILL));
                } else {
                    $GLOBALS['LIF_CHILD_PROCESSES'][] = $pid;
                    // Timeout check in master process
                    pcntl_signal(SIGCHLD, SIG_IGN);
                    do {
                        // check if child process exists now
                        if (shm_has_var($shm, JOB_STATUS)
                            && (shm_get_var($shm, JOB_STATUS) == 1)
                        ) {
                            $success = true;
                            break;
                        }

                        sleep(1);
                    } while (time() < $expire);

                    posix_kill($pid, SIGKILL);

                    if ($success) {
                        $this->outOfQueue();
                    } else {
                        $this->releaseCurrentJob();
                    }
                }

                @shm_remove($shm);

                return $success;
            } else {
                $status = call_user_func([$job, 'run']);
                if ($status) {
                    return $this->outOfQueue();
                }

                $this->releaseCurrentJob();

                return false;
            }
        } catch (\Exception $e) {
            exception($e);
        }
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
            $this->info('No queue jobs now.');
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

    protected function getTimer(int $pid = null)
    {
        if (!$this->timer || !($this->timer instanceof Timeout)) {
            $this->timer = new Timeout($pid);
        }

        return $this->timer->resetPid();
    }
}
