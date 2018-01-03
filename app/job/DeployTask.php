<?php

namespace Lif\Job;

class DeployTask extends \Lif\Core\Abst\Job
{
    protected $task          = null;
    protected $envType       = null;
    protected $statusSuccess = null;
    protected $statusFail    = null;
    protected $userSuccess   = null;
    protected $userFail      = null;
    protected $recycleEnv    = null;
    protected $envStatus     = null;
    protected $commands      = [];
    protected $buildable     = true;
    protected $recyclable    = true;

    public function setTask(int $task)
    {
        $this->task = $task;

        return $this;
    }

    public function setCommands(array $commands)
    {
        $this->commands = $commands;

        return $this;
    }

    public function setBuildable(bool $buildable = true)
    {
        $this->buildable = $buildable;

        return $this;
    }

    public function setRecyclable(bool $recyclable = true)
    {
        $this->recyclable = $recyclable;
        
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

    public function parseTaskStatus($task)
    {
        if (! $task->alive()) {
            return false;
        }

        $notMaster = ('master' != strtolower($task->branch));

        if ($notMaster) {
            $this->commands = [
                "git checkout -b {$task->branch}",
                "git pull origin {$task->branch} --no-edit",
            ];
        }

        switch (strtolower($task->status)) {
            case 'waitting_dep2test': {
                // Check if task aleay deploy to a env already
                $this->envType       = ['test', 'emrg'];
                $this->userSuccess   = $this->userFail = $task->last;
                $this->statusSuccess = 'waitting_confirm_env';
                $this->statusFail    = 'waitting_fix_test';
            } break;

            case 'waitting_dep2stage': {
                $this->envType       = 'stage';
                $this->userSuccess   = $this->findLastTester($task);
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'waitting_2nd_test';
                $this->statusFail    = 'waitting_fix_stage';
            } break;

            case 'waitting_dep2stablerc': {
                $this->envType       = 'rc';
                $this->userSuccess   = $task->creator;
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'waitting_regression';
                $this->statusFail    = 'waitting_fix_stablerc';
                $this->envStatus     = 'running';

                if ($notMaster) {
                    $this->commands = [
                        "git pull origin {$task->branch} --no-edit",
                    ];
                }
            } break;

            case 'waitting_dep2prod': {
                $this->envType       = 'prod';
                $this->userSuccess   = $task->creator;
                $this->userFail      = $this->findLastDeveloper($task);
                $this->statusSuccess = 'online';
                $this->statusFail    = 'waitting_fix_prod';
                if ($notMaster) {
                    $this->commands = [
                        // TODO: specialize production deploy commands
                    ];
                }
            } break;
            
            default: return false; break;
        }

        return true;
    }

    public function getEnv($task, $project)
    {
        if ($task->env) {
            if ($env = $task->environment(['type' => $this->envType])) {
                return $env;
            } else {
                $this->recycleEnv = $task->env;
            }
        }

        return $project->environments([], [
            'type'   => $this->envType,
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
        if (false
            || (! ($task = $this->getTask()))
            || (! $task->alive())
            || (! ($current = $task->current()))
            || (! $current->alive())
            || (! ci_equal($current->role, 'ops'))
            || (true !== $this->parseTaskStatus($task))
        ) {
            $tid = $this->task ?? '?';

            logging("Deploying task {$tid}: illegal job.");

            return true;
        }

        if (!($project = $task->project())
            || (! $project->alive())
            || (! ci_equal($project->type, 'web'))
        ) {
            $this->assign(
                $task,
                $task->current,
                $task->last,
                $this->statusFail,
                L('NO_PROJECT')
            );

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
        }

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

        $env->status = $this->getEnvStatus();
        $env->save();

        $res = $this->deploy(
            $server, $this->getDeployCommands(
                $env->path,
                $project,
                $task->config
            )
        );

        $notes = null;
        if (0 === ($res['num'] ?? false)) {
            $user   = $this->userSuccess;
            $status = $this->statusSuccess;
            $task->env = $env->id;
            $this->recycleOtherEnvs();
        } else {
            $user   = $this->userFail;
            $status = $this->statusFail;
            $env->status = 'running';
            $env->save();
            $notes  = $res['err'] ?? L('INNER_ERROR');
        }

        $this->assign($task, $task->current, $user, $status, $notes);

        return true;
    }

    public function deploy($server, $commands)
    {
        $location = strtolower($server->location);

        if ('remote' == $location) {
            $ssh2 = $this->getSSH2($server);
            $res  = $ssh2->exec($commands);
            unset($ssh2);

            return $res;
        } elseif ('local' == $location) {
            return shexec($commands);
        }

        return [
            'err' => L('ILLEGAL_SERVER_LOCATION_CONFIG'),
        ];
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
        string $status = null,
        string $notes = null
    )
    {
        $task->last    = $from;
        $task->current = $to;
        $task->status  = ($status ?? 'UNKNOWN_STATUS');

        if ($task->save()
            && $task->addTrending('assign', $from, $to, $notes)
        ) {
            enqueue(
                (new SendMailWhenTaskAssign)->setTask($task->id)
            )
            ->on('mail_send')
            ->try(3)
            ->timeout(30);

            return true;
        }
    }

    public function recycleOtherEnvs()
    {
        if ($this->recycleEnv
            && ($env = model(\Lif\Mdl\Environment::class, $this->recycleEnv))
            && $env->alive()
            && ($server = $env->server())
            && ($server->alive())
        ) {
            $this->deploy($server, $this->getEnvRecycleCommands($env->path));

            $env->status = 'running';
            $env->save();
        }
    }

    protected function getEnvRecycleCommands(string $path)
    {
        if (! $this->recyclable) {
            return [];
        }

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

    public function appendCommands(array &$cmds, array $appends = null)
    {
        if ($appends) {
            $cmds = array_filter(array_merge($cmds, $appends));
        }

        return $this;
    }

    public function appendBuildScript(array &$commands, string $script = null)
    {
        if ($buildScript = trim($script)) {
            if (preg_match('/(\ )+/u', $buildScript)) {
                $cmds = [$buildScript];
            } else {
                $cmds = @explode(
                    PHP_EOL,
                    file_get_contents(pathOf('root', $buildScript))
                );
            }

            if ($cmds) {
                $this->appendCommands($commands, $cmds);
            }
        }

        return $this;
    }

    public function appendConfigScript(
        array &$commands,
        string $script = null,
        string $config = null
    )
    {
        if ($api = trim($script) && ($config = trim($config))) {
            if (preg_match('/(\ )+/u', $api)) {
                $commands[] = "{$api} '{$config}'";
            } else {
                $commands[] = "chmod +x {$api}";
                $commands[] = "./{$api} '{$config}'";
            }
        }

        return $this;
    }

    private function appendBuildCommands(array &$commands, $project)
    {
        $this->appendBuildScript($commands, $project->build_script);
    }

    private function appendConfigCommands(array &$commands, $project, $config)
    {
        $this->appendConfigScript($commands, $project->config_api, $config);
    }

    public function getProjectDeployCommands(
        $project = null,
        string $config = null
    ) : array
    {
        $commands = [];

        if (!$this->buildable || !$project || !$project->alive()) {
            return$commands;
        }

        if (ci_equal($project->config_order, 'before')) {
            $this->appendConfigCommands($commands, $project, $config);
            $this->appendBuildCommands($commands, $project);
        } else {
            $this->appendBuildCommands($commands, $project);
            $this->appendConfigCommands($commands, $project, $config);
        }

        return $commands;
    }

    protected function getDeployCommands(
        string $path,
        $project = null,
        string $config = null
    ) : array
    {
        $commands = array_filter(array_merge(
            $this->getEnvRecycleCommands($path),
            $this->commands,
            $this->getProjectDeployCommands($project, $config)
        ));

        $this->appendCommands($commands, [
            'chown -R www:www `pwd`',
        ]);

        return $commands;
    }
}
