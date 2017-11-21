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
        $data = $this->request->all();
        $data['status']  = 'created';
        $data['creator'] = share('user.id');

        if (($status = $task->create($data))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
        } else {
            $msg    = lang('CREATED_FAILED', $status);
            $status = 'new';
        }

        share_error_i18n($msg);

        return redirect("/dep/tasks/{$status}");
    }

    public function update(TaskModel $task)
    {
        if (! $task->items()) {
            share_error_i18n('TASK_NOT_FOUND');
            redirect('/dep/tasks');
        }

        $status = (($err = $task->save($this->request->all())) >= 0)
        ? 'UPDATE_OK'
        : 'UPDATE_FAILED';

        $err = !is_integer($err) ? lang($err) : null;

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
