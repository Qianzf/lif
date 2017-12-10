<?php

namespace Lif\Job;

class DeployTask extends \Lif\Core\Abst\Job
{
    protected $task = null;
    protected $statusSuccess = null;
    protected $statusFail    = null;
    protected $userSuccess   = null;
    protected $userFail      = null;

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
            } break;

            case 'waitting_dep2prod': {
                $type = 'prod';
                $this->userSuccess   = $task->creator;
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'online';
                $this->statusFail    = 'waitting_fix_prod';
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

        if ($task->isAlive()) {
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
            || !$project->isAlive()
            || ('web' != strtolower($project->type))
        ) {
            return true;
        }

        if (!($env = $this->getEnv($task, $project)) || !$env->isAlive()) {
            $this->assign(
                $task,
                $task->current,
                $task->last,
                $this->statusFail,
                L('NO_ENV_FOUND')
            );

            return true;
        } else {
            db()->start();
            $env->status = 'locked';
            $task->env   = $env->id;
            if (($env->save() >= 0) && ($task->save() >= 0)) {
                db()->commit();
            } else {
                // TODO ... add log
                db()->rollback();

                $this->assign(
                    $task,
                    $task->current,
                    $task->last,
                    $this->statusFail,
                    L('INNER_ERROR')
                );

                return true;
            }
        }

        if (!($server = $env->server()) || !$server->isAlive()) {
            $this->assign(
                $task,
                $task->current,
                $task->last,
                $this->statusFail,
                L('NO_SERVER_FOUND')
            );

            return true;
        }

        $res = $this
        ->getSSH2($server->host)
        ->setPubkey($server->pubk)
        ->setPrikey($server->prik)
        ->connect([
            'hostkey' => 'ssh-rsa',
        ])
        ->exec($this->getDeployCommands($env->path, $branch));

        if (0 === ($res['num'] ?? false)) {
            $user   = $this->userSuccess;
            $status = $this->statusSuccess;
            $notes  = null;
        } else {
            $user   = $this->userFail;
            $status = $this->statusFail;
            $notes  = $res['err'] ?? L('INNER_ERROR');
        }

        $this->assign($task, $task->current, $user, $status, $notes);

        return true;
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

    protected function getDeployCommands(string $path, string $branch) : array
    {
        return [
            "cd {$path}",

            'git add -A',
            'git reset --hard HEAD',
            // 'git checkouts master',

            // 'git branch | grep -v master | xargs git branch -D',
            
            // need newer version of git for `--no-edit` option
            // "git pull origin {$branch} --no-edit",

            "git pull origin master --no-edit",
        ];
    }
}
