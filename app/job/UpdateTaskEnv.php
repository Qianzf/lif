<?php

namespace Lif\Job;

class UpdateTaskEnv extends \Lif\Core\Abst\Job
{
    private $origin = null;
    private $task   = null;
    private $env    = null;
    private $server = null;
    private $url    = null;
    private $branch = null;
    private $token  = null;

    public function prepareWhenOriginIsLdtdf()
    {
        if (true
            && ispint($this->task, false)
            && ($task = $this->getCommonQueryPart()
                ->where([
                    't.id' => $this->task,
                ])
                ->first()
            )
            && $this->taskEnvConditionIsReady($task)
        ) {
            return $task;
        }

        return false;
    }

    public function prepareWhenOriginIsGitlab()
    {
        if (!$this->branch || !$this->url) {
            return false;
        }

        $branch = str_replace('refs/heads/', '', $this->branch);

        if (true
            && ($task = $this->getCommonQueryPart()
                ->where([
                    't.branch' => $branch,
                    'p.url'    => $this->url,
                ])
                ->first()
            )
            && (($task['token'] ?? null) == $this->token)
            && $this->taskEnvConditionIsReady($task)
        ) {
            return $task;
        }

        return false;
    }

    private function taskEnvConditionIsReady($task)
    {
        return (true
            && $this->updatableTaskStatus($task['status'] ?? null)
            && ($env = ($task['env'] ?? null))
            && ($this->env = model(\Lif\Mdl\Environment::class, $env))
            && $this->env->alive()
            && ($this->server = $this->env->server())
            && $this->server->alive()
        );
    }

    private function getCommonQueryPart()
    {
        return db()
        ->table('task', 't')
        ->leftJoin(['project', 'p'], 't.project', 'p.id')
        ->select(
            't.id',
            't.env',
            't.status',
            't.branch',
            't.config',
            'p.token',
            'p.build_script',
            'p.config_api',
            'p.config_order'
        )
        ->where('t.env', '>', 0)
        ->where([
            'p.type' => 'web',
        ]);
    }

    // 1. Find out task env by related branch and url
    // 2. Check secure token in headers if configed
    // 3. Update tasks env when status is correct
    public function run() : bool
    {
        if (empty_safe($origin = ucfirst(strtolower($this->origin)))) {
            return true;
        }

        if (! ($user = $this->findOneOperator())) {
            excp('Missing system operator');
        }

        $prepare = "prepareWhenOriginIs{$origin}";

        if (! method_exists($this, $prepare)) {
            excp("Missing origin prepare handler: {$prepare}");
        }

        if (false === ($task = call_user_func([$this, $prepare]))) {
            return true;
        }

        if (empty_safe($path = $this->env->path)) {
            excp('Missing env execute path');
        }

        if (empty_safe($branch = $task['branch'])) {
            excp('Missing task branch');
        }

        $commands = [
            "cd {$path}",
            'git add -A',
            'git reset --hard HEAD',
            'git checkout master',
            '(git branch | grep -v "master" | xargs git branch -D &>/dev/null || echo skip &>/dev/null)',
            'git pull origin master --no-edit',
        ];

        if ('master' != strtolower($branch)) {
            $commands[] = "git checkout -b {$branch}";
            $commands[] = "git pull origin {$branch} --no-edit";
        }

        $deployer = $this
        ->makeDeployer()
        ->setRecyclable(false)
        // ->setBuildable(false)
        // ->setCommands([
        // ])
        ;

        $config      = ($task['config'] ?? null);
        $configApi   = ($task['config_api'] ?? null);
        $buildScript = ($task['build_script'] ?? null);

        if (ci_equal(($task['config_order'] ?? null), 'before')) {
            $deployer
            ->appendConfigScript($commands, $configApi)
            ->appendBuildScript($commands, $buildScript)
            ;
        } else {
            $deployer
            ->appendBuildScript($commands, $buildScript)
            ->appendConfigScript($commands, $configApi)
            ;
        }

        $res = $deployer
        ->appendCommands($commands, [
            'chown -R www:www `pwd`',
        ])
        ->deploy($this->server, $commands);

        $err    = null;
        $status = 'SUCCESS';

        if (0 != $res['num']) {
            $status = 'FAILED';
            $err    = $res['err'] ?? L('UNKNOWN_ERROR');
        }

        db()->table('trending')->insert([
            'at'        => fndate(),
            'user'      => $user,
            'action'    => 'update_branch',
            'ref_state' => "UPDATE_TASK_ENV_{$status}",
            'ref_type'  => 'task',
            'ref_id'    => ($task['id'] ?? null),
            'notes'     => $err,
        ]);

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

    public function setOrigin(string $origin)
    {
        $this->origin = $origin;

        return $this;
    }

    public function setTask(int $task)
    {
        $this->task = $task;

        return $this;
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
