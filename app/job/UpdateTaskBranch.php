<?php

namespace Lif\Job;

class UpdateTaskBranch extends \Lif\Core\Abst\Job
{
    private $url    = null;
    private $branch = null;
    private $token  = null;

    // 2. Find out task env by related branch
    // 3. Check secure token in headers if configed
    // 4. Update tasks env when status is correct
    public function run() : bool
    {
        if (!$this->branch || !$this->url) {
            return true;
        }

        $branch = str_replace('refs/heads/', '', $this->branch);

        if ($task = db()
            ->table('task', 't')
            ->leftJoin(['project', 'p'], 't.project', 'p.id')
            ->select('t.id', 't.env', 'p.token')
            ->where('t.env', '>', 0)
            ->where([
                't.branch' => $branch,
                'p.url'    => $this->url,
                // 'status'   => [
                // ],
            ])
            ->first()
        ) {
            if ((($task['token'] ?? null) == $this->token)
                && ($env = ($task['env'] ?? null))
                && ($env = model(\Lif\Mdl\Environment::class, $env))
                && $env->alive()
                && ($server = $env->server())
                && $server->alive()
            ) {
                if (! ($user = $this->findOneOperator())) {
                    excp('Missing system operator');
                }

                $res = $this
                ->makeDeployer()
                ->setRecyclable(false)
                ->setBuildable(false)
                // ->setCommands([
                // ])
                ->deploy($server, [
                    "cd {$env->path}",
                    'git add -A',
                    'git reset --hard HEAD',
                    "git pull origin {$branch} --no-edit"
                ]);

                $status = (0 == $res['num'])
                ? 'UPDATE_TASK_BRANCH_SUCCESS'
                : 'UPDATE_TASK_BRANCH_FAILED';

                db()->table('trending')->insert([
                    'at'        => date('Y-m-d H:i:s'),
                    'user'      => $user,
                    'action'    => 'update_branch',
                    'ref_state' => $status,
                    'ref_type'  => 'task',
                    'ref_id'    => $task['id'],
                    'notes'     => $err,
                ]);
            }
        }

        return true;
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
