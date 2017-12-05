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
        // Check if task aleay deploy to a env already
        if ($env = $task->environment()) {
            return $env;
        }

        switch (strtolower($task->status)) {
            case 'waitting_dep2test':
            case 'waitting_update2test': {
                $type = ['test', 'emrg'];
            } break;

            case 'waitting_dep2stage':
            case 'waitting_update2stage': {
                $type = 'stage';
            } break;
            
            default: return false; break;
        }

        return $project->environments([], [
            'type'   => $type,
            'status' => 'running',
            'task'   => [
                'key' => '<',
                'val' => 1,
            ],
        ], 1);
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
        if (!($task = $this->getTask()) || !$task->isAlive()) {
            return true;
        }

        if (!($project = $task->project()) || !$project->isAlive()) {
            return true;
        }

        if (!($env = $this->getEnv($task, $project)) || !$env->isAlive()) {
            return true;
        }

        if (!($server = $env->server()) || !$server->isAlive()) {
            return true;
        }
        
        pr(
            $this
            ->getSSH2($server->host)
            ->setPubkey($server->pubk)
            ->setPrikey($server->prik)
            ->connect([
                'hostkey' => 'ssh-rsa',
            ])
            ->exec([
                'whoami',
                "cd {$env->path}",
                'pwd',
                'git branch',
                'git status',
            ])
        );

        return true;
    }
}
