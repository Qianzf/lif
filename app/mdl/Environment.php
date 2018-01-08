<?php

namespace Lif\Mdl;

class Environment extends Mdl
{
    protected $table = 'environment';
    protected $rules = [
        'host' => 'need|host',
        'type' => ['need|ciin:test,emrg,stage,rc,prod', 'test'],
        'path' => 'string',
        'project' => 'need|int|min:1',
        'server'  => 'need|int|min:1',
        'desc'    => 'string',
        'status'  => 'ciin:running,stopped,locked'
    ];

    public function hasHostBefore(string $host = null)
    {
        return $this
        ->whereHost($host)
        ->count() > 0;
    }

    public function getTaskBranchHTML()
    {
        if ($tasks = $this->tasks()) {
            $html = '';
            foreach ($tasks as $task) {
               $route = lrn("tasks/{$task->id}");
               $html .= "<a href='{$route}'>{$task->branch} / {$task->id}</a>";

               if (false !== next($tasks)) {
                    $html .= '; ';
               }
            }

            return $html;
        }

        return '-';
    }

    // For App only now
    public function setRegressionPass()
    {
        // TODO
    }

    // For App only now
    public function setRegressionUnpass(Task $task = null)
    {
        if (! $this->alive()) {
            excp('Regression Environment not exists');
        }

        $query = db()
        ->table('task')
        ->whereStatus([
            'REGRESSION_TESTING',
            'regression_testing',
        ])
        ->whereEnv($this->id);

        if ($task) {
            if (! $task->alive()) {
                excp('Regression Environment related task not exists');
            }

            $updateOther = $query->update('status', 'stablerc_back2other');
            $updateSelf  = $query
            ->whereId($task->id)
            ->update('status', 'stablerc_back2self');

            // TODO find and assign to last developer
            // ...

            return (
                ($updateOther >= 0) && ($updateSelf >= 0)
            );
        }

        return ($query->update('status', 'waitting_newfix_stablerc') >= 0);
    }

    public function startRegressionTest()
    {
        if (! $this->alive()) {
            excp('Regression Environment not exists');
        }

        $res = db()
        ->table('task')
        ->whereStatus([
            'WAITTING_REGRESSION',
            'waitting_regression',
        ])
        ->whereEnv($this->id)
        ->update('status', 'regression_testing');

        return ($res >= 0);
    }

    public function getRegressionableStatus()
    {
        return [
            'WAITTING_REGRESSION',
            'waitting_regression',
            'REGRESSION_TESTING',
            'regression_testing',
        ];
    }

    public function getWebRegressions(bool $model = true)
    {
        $regressions = db()
        ->table($this->getTable(), 'e')
        ->leftJoin(['task', 't'], 'e.id', 't.env')
        ->select('e.*')
        ->where('t.status', $this->getRegressionableStatus())
        ->group('t.env')
        ->get();

        if ($model) {
            $this->__toModel($regressions);
        }

        return $regressions;
    }

    public function tasks(array $fwhere = [], $lwhere = [])
    {
        return $this->hasMany([
            'model' => Task::class,
            'lk' => 'id',
            'fk' => 'env',
            'fwhere' => $fwhere,
            'lwhere' => $lwhere,
        ]);
    }

    public function project(string $attr = null)
    {
        if ($project = $this->belongsTo(
            Project::class,
            'project',
            'id'
        )) {
            return $attr ? $project->$attr : $project;   
        }
    }

    public function projects()
    {
        return $this->hasMany(
            Project::class,
            'project',
            'id'
        );   
    }

    public function server(string $key = null)
    {
        if ($server = $this->belongsTo(
            Server::class,
            'server',
            'id'
        )) {
            return $key ? $server->$key : $server;
        }
    }
}
