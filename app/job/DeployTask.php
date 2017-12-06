<?php

namespace Lif\Job;

class DeployTask extends \Lif\Core\Abst\Job
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

        // Check if task aleay deploy to a env already
        if ($env = $task->environment()) {
            return $env;
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
        if (!($task = $this->getTask())) {
            return true;
        }

        if ($task->isAlive()) {
            if (! ($branch = trim($task->branch))) {
                // Update task status and add into trending
                if ($status = $this->getResponseTaskStatus($task)) {
                    $task->assign([
                        'assign_from'  => $task->current,
                        'assign_to'    => $task->last,
                        'status'       => $status,
                        'assign_notes' => L('MISSING_TASK_BRANCH'),
                    ]);
                }

                return true;
            }
        } else {
            return true;
        }

        if (!($project = $task->project())
            || !$project->isAlive()
            || ('web' != strtolower($project->type))
        ) {
            return true;
        }

        if (!($env = $this->getEnv($task, $project)) || !$env->isAlive()) {
            pr($env);
            return true;
        } else {
            $env->task   = $task->id;
            $env->status = 'locked';
            $env->save();
        }

        if (!($server = $env->server()) || !$server->isAlive()) {
            return true;
        }
        
        $res = $this
        ->getSSH2($server->host)
        ->setPubkey($server->pubk)
        ->setPrikey($server->prik)
        ->connect([
            'hostkey' => 'ssh-rsa',
        ])
        ->exec($this->getDeployCommands($env->path, $task->branch));

        if (0 === ($res['num'] ?? false)) {
            $from = $task->current;
            $to   = $task->last;
            $status = 'OK';
            $notes = null;
        } else {
            $from = $task->current;
            $to   = $task->last;
            $status = $this->getResponseTaskStatus($task);
            $notes  = $res['err'] ?? L('INNER_ERROR');
        }

        $task->assign([
            'assign_from'  => $from,
            'assign_to'    => $to,
            'status'       => $status,
            'assign_notes' => $notes,
        ]);

        return true;
    }

    protected function getDeployCommands(string $path, string $branch) : array
    {
        return [
            "cd {$path}",

            'git adds -A',
            'git resets --hard HEAD',
            // 'git checkouts master',

            // 'git branch | grep -v master | xargs git branch -D',
            
            // need newer version of git for `--no-edit` option
            "git pulls origin {$branch} --no-edit",
        ];
    }

    private function getResponseTaskStatus($task)
    {
        switch (strtolower($task->status)) {
            case 'waitting_dep2test':
            case 'waitting_update2test': {
                return 'waitting_fix_test';
            } break;

            case 'waitting_dep2stage':
            case 'waitting_update2stage': {
                return 'waitting_fix_stage';
            } break;
            
            default: return false; break;
        }

        return false;
    }
}
