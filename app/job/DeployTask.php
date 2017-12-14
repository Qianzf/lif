<?php

namespace Lif\Job;

class DeployTask extends \Lif\Core\Abst\Job
{
    protected $task = null;
    protected $statusSuccess = null;
    protected $statusFail    = null;
    protected $userSuccess   = null;
    protected $userFail      = null;
    protected $recycleEnv    = null;
    protected $envStatus     = null;
    protected $commands      = [];

    public function setTask(int $task)
    {
        $this->task = $task;

        return $this;
    }

    public function getTask()
    {
        return new \Lif\Mdl\Task($this->task);
    }

    public function getSSH2($server)
    {
        return (
            new \Lif\Core\Lib\Connect\SSH2($server->host)
        )
        ->setPubkey($server->pubk)
        ->setPrikey($server->prik)
        ->connect([
            'hostkey' => 'ssh-rsa',
        ]);
    }

    public function getEnv($task, $project)
    {
        $this->commands = [
            "git checkout -b {$task->branch}",
            // "git pull origin {$task->branch} --no-edit",
        ];

        switch (strtolower($task->status)) {
            case 'waitting_dep2test': {
                // Check if task aleay deploy to a env already
                $type = ['test', 'emrg'];
                $this->userSuccess   = $this->userFail = $task->last;
                $this->statusSuccess = 'waitting_confirm_env';
                $this->statusFail    = 'waitting_fix_test';
            } break;

            case 'waitting_dep2stage': {
                $type = 'stage';
                $this->userSuccess   = $this->findLastTester($task);
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'waitting_2nd_test';
                $this->statusFail    = 'waitting_fix_stage';
            } break;

            case 'waitting_dep2stablerc': {
                $type = 'rc';
                $this->userSuccess   = $task->creator;
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'waitting_regression';
                $this->statusFail    = 'waitting_fix_stablerc';
                $this->envStatus     = 'running';
                $this->commands = [
                    // "git pull origin {$task->branch} --no-edit",
                ];
            } break;

            case 'waitting_dep2prod': {
                $type = 'prod';
                $this->userSuccess   = $task->creator;
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'online';
                $this->statusFail    = 'waitting_fix_prod';
                $this->commands = [
                ];
            } break;
            
            default: return false; break;
        }

        if ($task->env) {
            if ($env = $task->environment(['type' => $type])) {
                return $env;
            } else {
                $this->recycleEnv = $task->env;
            }
        }

        return $project->environments([], [
            'type'   => $type,
            'status' => 'running',
        ], 1);
    }

    protected function findLastTester($task)
    {
        return $this->findNextUser($task, 'test');
    }

    protected function findLastDeveloper($task)
    {
        return $this->findNextUser($task, 'dev');
    }

    protected function findNextUser($task, string $role)
    {
        $last = db()
        ->table('trending', 't')
        ->leftJoin('user u', 't.user', 'u.id')
        ->select('t.user')
        ->where([
            't.ref_type' => 'task',
            't.ref_id'   => $task->id,
            'u.role' => $role,
        ])
        ->sort([
            't.at' => 'desc',
        ])
        ->first();

        return $last['user'] ?? $task->last;
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

        if ($task->alive()) {
            if (! ($branch = trim($task->branch))) {
                $this->assign(
                    $task,
                    $task->current,
                    $this->findLastDeveloper($task),
                    $this->statusFail,
                    L('TASK_BRANCH_NOT_FOUND')
                );

                return true;
            }
        } else {
            return true;
        }

        if (!($project = $task->project())
            || !$project->alive()
            || ('web' != strtolower($project->type))
        ) {
            return true;
        }

        if (!($env = $this->getEnv($task, $project)) || !$env->alive()) {
            $this->assign(
                $task,
                $task->current,
                $task->last,
                $this->statusFail,
                L('NO_ENV_FOUND')
            );

            return true;
        } else {
            $env->status = $this->getEnvStatus();
            $env->save();
        }

        if (!($server = $env->server()) || !$server->alive()) {
            $this->assign(
                $task,
                $task->current,
                $task->last,
                $this->statusFail,
                L('NO_SERVER_FOUND')
            );

            return true;
        }

        $ssh2 = $this->getSSH2($server);
        $res  = $ssh2->exec($this->getDeployCommands($env->path, $branch));

        if (0 === ($res['num'] ?? false)) {
            $user   = $this->userSuccess;
            $status = $this->statusSuccess;
            $notes  = null;
            $task->env = $env->id;
            $task->save();
            $this->recycleOtherEnvs();
        } else {
            $user   = $this->userFail;
            $status = $this->statusFail;
            $notes  = $res['err'] ?? L('INNER_ERROR');
        }

        $this->assign($task, $task->current, $user, $status, $notes);

        return true;
    }

    protected function getEnvStatus()
    {
        return $this->envStatus ?? 'locked';
    }

    // Update task status and add into trending
    private function assign(
        $task, 
        int $from, 
        int $to, 
        string $status,
        string $notes = null
    )
    {
        $task->assign([
            'assign_from'  => $from,
            'assign_to'    => $to,
            'action'       => $status,
            'assign_notes' => $notes,
        ]);
    }

    public function recycleOtherEnvs()
    {
        if ($this->recycleEnv
            && ($env = model(\Lif\Mdl\Environment::class, $this->recycleEnv))
            && $env->alive()
            && ($server = $env->server())
            && ($server->alive())
        ) {
            $this
            ->getSSH2($server)
            ->exec($this->getEnvRecycleCommands($env->path));

            $env->status = 'running';
            $env->save();
        }
    }

    protected function getEnvRecycleCommands(string $path)
    {
        return [
            "cd {$path}",
            'git add -A',
            'git reset --hard HEAD',
            'git checkout master',
            'git branch | grep -v "master" | xargs git branch -D &>/dev/null || echo skip &>/dev/null',
            
            // need newer version of git for `--no-edit` option
            'git pull origin master --no-edit',
        ];
    }

    protected function getDeployCommands(string $path, string $branch) : array
    {
        $commands = array_merge(
            $this->getEnvRecycleCommands($path),
            $this->commands
        );

        $commands[] = 'chown -R www:www `pwd`';

        return $commands;
    }
}
