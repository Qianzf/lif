<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;
use Lif\Mdl\{User, Project};

class Task extends Ctl
{
    public function assign()
    {
        $id = $this->vars[0] ?? null;

        return redirect("/dep/tasks/{$id}");
    }

    public function assignTo()
    {
        dd($this->request->all());
    }

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

    public function add(TaskModel $task, Project $project)
    {
        share('hide-search-bar', true);
        
        view('ldtdf/task/edit')
        ->withTaskProjectsEditableTrendings(
            $task,
            $project->all(),
            true,
            $task->trendings()
        );
    }

    public function edit(TaskModel $task, Project $project)
    {
        if (! $task->isAlive()) {
            share_error_i18n('NO_TASK');
            return redirect(share('url_previous'));
        }

        return $this->add($task, $project);
    }

    public function info(TaskModel $task)
    {
        if (! $task->isAlive()) {
            share_error_i18n('NO_TASK');
            return redirect(share('url_previous'));
        }

        $querys = $this->request->gets;
        legal_or($querys, [
            'trending' => ['in:asc,desc', 'asc']
        ]);

        $user       = share('user.id');
        $editable   = ($task->creator == $user);
        $assignable = ($task->canBeAssignedBy($user));
        
        view("ldtdf/task/info")
        ->withTaskProjectTrendingsEditableAssignable(
            $task,
            $task->project(),
            $task->trendings($querys),
            $editable,
            $assignable
        );
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

        if (!empty_safe($err = $task->save($this->request->posts))
            && is_numeric($err)
            && ($err >= 0)
        ) {
            if ($err > 0) {
                $status = 'UPDATE_OK';
                $task->addTrending('update');
            } else {
                $status = 'UPDATED_NOTHING';
            }
        } else {
            $status = 'UPDATE_FAILED';
        }

        $err = is_integer($err) ? null : lang($err);

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
