<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;

class Task extends Ctl
{
    public function index(TaskModel $task)
    {
        view('ldtdf/task/index')->withTasks($task->all());
    }

    public function edit(TaskModel $task)
    {
        share('hide-search-bar', true);
        
        view('ldtdf/task/edit')->withTask($task);
    }

    public function create(TaskModel $task)
    {
        dd($task->create($this->request->all()));
    }

    public function update(TaskModel $task)
    {
        if (! $task->items()) {
            share_error_i18n('TASK_NOT_FOUND');
            redirect('/dep/tasks');
        }

        $status = (($err = $task->save($this->request->all())) > 0)
        ? 'UPDATE_OK'
        : 'UPDATE_FAILED';

        $err = !is_integer($err) ? lang($err) : null;

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
