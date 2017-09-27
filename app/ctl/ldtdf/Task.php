<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;

class Task extends Ctl
{
    public function index(TaskModel $task)
    {
        view('ldtdf/tasks')->withTasks($task->all());
    }

    public function detail(TaskModel $task)
    {
    }
}
