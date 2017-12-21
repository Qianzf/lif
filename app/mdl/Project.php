<?php

namespace Lif\Mdl;

class Project extends Mdl
{
    protected $table = 'project';
    protected $rules = [
        'name'  => 'need|string',
        'type'  => ['need|ciin:web,app', 'web'],
        'url'   => 'need|string',
        'vcs'   => ['need|ciin:git', 'git'],
        'desc'  => 'string',
        'token' => ['string', null],
        'config_api'    => 'string',
        'deploy_script' => ['string', null],
    ];

    public function tasks(array $fwhere = [], $lwhere = [])
    {
        return $this->hasMany([
            'model' => Task::class,
            'lk' => 'id',
            'fk' => 'project',
            'fwhere' => $fwhere,
            'lwhere' => $lwhere,
        ]);
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
            excp('Regression Project not exists');
        }

        $query = db()
        ->table('task')
        ->whereStatus([
            'REGRESSION_TESTING',
            'regression_testing',
        ])
        ->whereProject($this->id);

        if ($task) {
            if (! $task->alive()) {
                excp('Regression project related task not exists');
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

    public function getAppRegressions(bool $model = true)
    {
        $regressions = db()
        ->table($this->getTable(), 'p')
        ->leftJoin(['task', 't'], 'p.id', 't.project')
        ->select('p.*')
        ->where('t.status', $this->getRegressionableStatus())
        ->where('p.type', 'app')
        ->group('t.project')
        ->get();

        if ($model) {
            $this->__toModel($regressions);
        }

        return $regressions;
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

    public function deployable()
    {
        if ($this->alive()) {
            return (strtolower($this->type) == 'web');
        }

        return false;
    }

    public function environments(
        array $lwhere = [],
        array $fwhere = [],
        int $take = 10,
        int $from = 0
    )
    {
        return $this->hasMany([
            'model' => Environment::class,
            'lk' => 'id',
            'fk' => 'project',
            'from' => $from,
            'take' => $take,
            'lwhere' => $lwhere,
            'fwhere' => $fwhere,
        ]);
    }
}
