<?php

namespace Lif\Ctl\Ldtdf\Tester;

use Lif\Mdl\{Environment, Task};

class Regression extends \Lif\Ctl\Ldtdf\Ctl
{
    public function setUnpass(Environment $env)
    {
        $env->setRegressionUnpass();

        return redirect("/dep/test/regressions");
    }

    public function setTaskUnpass(Environment $env, Task $task)
    {
        $env->setRegressionUnpass($task);

        return redirect("/dep/test/regressions/{$env->id}");
    }

    public function startTest(Environment $env)
    {
        $env->startRegressionTest();

        return redirect("/dep/test/regressions");
    }

    public function index(Environment $env)
    {
        return view('ldtdf/tester/regressions')
        ->withRegressions($env->getRegressions());
    }

    public function relateTasks(Environment $env)
    {
        return view('ldtdf/tester/regression')
        ->withEnvTasks($env, $env->tasks([
            'status' => $env->getRegressionableStatus(),
        ]));
    }
}
