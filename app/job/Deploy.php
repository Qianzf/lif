<?php

namespace Lif\Job;

class Deploy extends \Lif\Core\Abst\Job
{
    protected $task = null;

    public function setTask(int $task)
    {
        $this->task = $task;

        return $this;
    }

    public function getTask()
    {
        return new \Lif\Mdl\Task($this->task);
    }

    public function getSSH2($host)
    {
        return new \Lif\Core\Lib\Connect\SSH2($host);
    }

    public function getEnv($task, $project)
    {
        switch (strtolower($task->status)) {
            case 'waitting_dep2test': {
                if ($env = $task->environment()) {
                    return $env;
                }

                return $project->environments([], [
                    'type'   => ['test', 'emrg'],
                    'status' => 'running',
                    'task'   => [
                        'key' => '<',
                        'val' => 1,
                    ],
                ], 1);
            } break;

            case 'waitting_update2test': {
                // Check if task aleay deploy to a env already
                if ($env = $task->environment()) {
                    return $env;
                }
            } break;

            case 'waitting_dep2stage': {

            } break;

            case 'waitting_update2stage': {

            } break;
            
            default: break;
        }

        return false;
    }

    // 0. Find out task to deploy
    // 1. Find task related project `A` and it's deploy script
    // 2. Select one available environment `B` for project `A`
    //    according to current task status and lock its status
    // 3. Find server with project path `C` for environment `B`
    // 4. Connect to server and pull task branch and execute deploy script
    // 5. Switch deploy status and assign to proper person with remarks
    public function run() : bool
    {
        if (! ($task = $this->getTask())->isAlive()) {
            return true;
        }

        if (! ($project = $task->project())->isAlive()) {
            return true;
        }

        if (! ($environment = $this->getEnv($task, $project))->isAlive()) {
            return true;
        }

        if (! ($server = $environment->server())->isAlive()) {
            return true;
        }
        
        pr(
            $this
            ->getSSH2($server->host)
            ->setPubkey($server->pubk)
            ->setPrikey($server->prik)
            ->connect([
                'hostkey' => 'ssh-rsa',
            ])->exec([
                'whoami',
                "cd {$environment->path}",
                'pwd',
                'git branch',
                'git status',
            ])
        );

        return true;
    }
}
