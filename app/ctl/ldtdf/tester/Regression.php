<?php

namespace Lif\Ctl\Ldtdf\Tester;

use Lif\Mdl\{Environment, Project, Task};

class Regression extends \Lif\Ctl\Ldtdf\Ctl
{
    public function setEnvPass(Environment $env)
    {
        $env->setRegressionPass();

        return redirect(lrn('test/regressions'));
    }

    public function setProjectPass(Project $project)
    {
        $project->setRegressionPass();

        return redirect(lrn('test/regressions'));
    }

    public function setEnvUnpass(Environment $env)
    {
        $env->setRegressionUnpass();

        return redirect(lrn('test/regressions'));
    }

    public function setProjectUnpass(Project $project)
    {
        $project->setRegressionUnpass();

        return redirect(lrn('test/regressions'));
    }

    public function setEnvTaskUnpass(Environment $env, Task $task)
    {
        $env->setRegressionUnpass($task);

        return redirect(lrn("test/regressions/env/{$env->id}"));
    }

    public function setProjectTaskUnpass(Project $project, Task $task)
    {
        $project->setRegressionUnpass($task);

        return redirect(lrn("test/regressions/project/{$project->id}"));
    }

    public function startTest(Environment $env)
    {
        $env->startRegressionTest();

        return redirect(lrn('test/regressions'));
    }

    public function index(Environment $env, Project $project)
    {
        return view('ldtdf/tester/regressions')
        ->withEnvsProjects(
            $env->getWebRegressions(),
            $project->getAppRegressions()
        );
    }

    public function relateTasksOfEnv(Environment $env)
    {
        return view('ldtdf/tester/regression/env')
        ->withEnvTasks($env, $env->tasks([
            'status' => $env->getRegressionableStatus(),
        ]));
    }

    public function relateTasksOfProject(Project $project)
    {
        return view('ldtdf/tester/regression/project')
        ->withProjectTasks($project, $project->tasks([
            'status' => $project->getRegressionableStatus(),
        ]));
    }
}
