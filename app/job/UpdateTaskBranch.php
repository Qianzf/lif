<?php

namespace Lif\Job;

class UpdateTaskBranch extends \Lif\Core\Abst\Job
{
    private $url    = null;
    private $branch = null;
    private $token  = null;

    // 1. Find out task env by related branch and url
    // 2. Check secure token in headers if configed
    // 3. Update tasks env when status is correct
    public function run() : bool
    {
        if (!$this->branch || !$this->url) {
            return true;
        }

        $branch = str_replace('refs/heads/', '', $this->branch);

        if ($task = db()
            ->table('task', 't')
            ->leftJoin(['project', 'p'], 't.project', 'p.id')
            ->select('t.id', 't.env', 't.status', 'p.token', 'p.build_script')
            ->where('t.env', '>', 0)
            ->where([
                't.branch' => $branch,
                'p.url'    => $this->url,
                'p.type'   => 'web',
            ])
            ->first()
        ) {
            if ((($task['token'] ?? null) == $this->token)
                && $this->updatableTaskStatus($task['status'] ?? null)
                && ($env = ($task['env'] ?? null))
                && ($env = model(\Lif\Mdl\Environment::class, $env))
                && $env->alive()
                && ($server = $env->server())
                && $server->alive()
            ) {
                if (! ($user = $this->findOneOperator())) {
                    excp('Missing system operator');
                }

                $commands = [
                    "cd {$env->path}",
                    'git add -A',
                    'git reset --hard HEAD',
                    "git pull origin {$branch} --no-edit"
                ];

                $res = $this
                ->makeDeployer()
                ->setRecyclable(false)
                // ->setBuildable(false)
                // ->setCommands([
                // ])
                ->appendBuildScript($commands, ($task['build_script'] ?? null))
                ->deploy($server, $commands);

                $err = null;
                $status = 'SUCCESS';

                if (0 != $res['num']) {
                    $status = 'FAILED';
                    $err    = $res['err'] ?? L('UNKNOWN_ERROR');
                }

                db()->table('trending')->insert([
                    'at'        => fndate(),
                    'user'      => $user,
                    'action'    => 'update_branch',
                    'ref_state' => "UPDATE_TASK_BRANCH_{$status}",
                    'ref_type'  => 'task',
                    'ref_id'    => $task['id'],
                    'notes'     => $err,
                ]);
            }
        }

        return true;
    }

    public function updatableTaskStatus(string $status = null)
    {
        if (! $status) {
            return false;
        }

        // TODO
        // Define updatable task status
        return true;

        return in_array(strtolower($status), [
            'env_confirmed',
            'fixing_prod',
            'fixing_stablerc',
            'fixing_stablercback',
            'fixing_stage',
            'fixing_stageback',
            'fixing_test',
            'fixing_testback',
            'waitting_1st_test',
            'waitting_2nd_test',
        ]);
    }

    public function findOneOperator()
    {
        $operator = db()
        ->table('user')
        ->select('id')
        ->whereRole('ops')
        ->first();

        return $operator['id'] ?? false;
    }

    public function makeDeployer()
    {
        return new DeployTask;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function setBranch(string $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    public function setToken(string $token = null)
    {
        $this->token = $token;

        return $this;
    }
}
