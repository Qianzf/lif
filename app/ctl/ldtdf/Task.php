<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;
use Lif\Mdl\User;

class Task extends Ctl
{
    public function index(TaskModel $task, User $user)
    {
        $querys = $this->request->all();
        legal_or($querys, [
            'user' => ['int|min:1', null],
        ]);

        if ($uid = $querys['user']) {
            $task = $task->whereCreator($uid);
        }

        $tasks = $task->get();

        view('ldtdf/task/index')->withRecordsTasksUsers(
            $task->count(),
            $tasks,
            $user->getNonAdmin()
        );
    }

    public function add(TaskModel $task)
    {
        share('hide-search-bar', true);
        
        view('ldtdf/task/edit')->withTask($task);
    }

    public function edit(TaskModel $task)
    {
        $error = $back2last = null;
        if (! $task->isAlive()) {
            $error     = lang('NO_TASK');
            $back2last = share('url_previous');
        }

        shares([
            'hide-search-bar' => true,
            '__error'   => $error,
            'back2last' => $back2last,
        ]);

        $action = ($task->creator == share('user.id')) ? 'edit' : 'info';
        
        view("ldtdf/task/{$action}")->withTask($task);
    }

    public function create(TaskModel $task)
    {
        $data = $this->request->all();dd($data);
        $data['status']  = 'created';
        $data['creator'] = share('user.id');

        if (($status = $task->create($data))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
            $task->addTrending('create');
        } else {
            $msg    = lang('CREATED_FAILED', $status);
            $status = 'new';
        }

        share_error_i18n($msg);

        return redirect("/dep/tasks/{$status}");
    }

    public function update(TaskModel $task)
    {
        if ($task->creator != share('user.id')) {
            share_error_i18n('UPDATE_PERMISSION_DENIED');
            redirect($this->route);
        }

        if (! $task->items()) {
            share_error_i18n('TASK_NOT_FOUND');
            redirect('/dep/tasks');
        }

        if (!empty_safe($err = $task->save($this->request->all()))
            && is_numeric($err)
            && ($err >= 0)
        ) {
            $status = 'UPDATE_OK';
            if ($err > 0) {
                $task->addTrending('update');
            }
        } else {
            $status = 'UPDATE_FAILED';
        }

        $err = is_integer($err) ? null : lang($err);

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
