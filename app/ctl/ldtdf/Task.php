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

        $params = $this->request->all();

        if (true === ($err = validate($params, [
            'title'  => 'string',
            'status' => 'int|min:1|max:3',
        ]))) {
            $task->title  = $params['title'];
            $task->status = $params['status'];

            if ($task->save()) {
                share_error_i18n('UPDATE_SUCCESS');
            } else {
                share_error_i18n('UPDATE_FAILED');
            }
        } else {
            share_error_i18n('CLIENT_DATA_ILLEGAL');
        }

        redirect($this->route);
    }
}
