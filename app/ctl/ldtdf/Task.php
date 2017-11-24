<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;
use Lif\Mdl\{User, Project, Trending};

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
        $projects = $project->all();

        share('hide-search-bar', true);
        
        view('ldtdf/task/edit')->withTaskProjects($task, $projects);
    }

    public function edit(TaskModel $task, Project $proj, Trending $trending)
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

        $action   = 'info';
        $projects = $project = null;

        if ($task->creator == share('user.id')) {
            $action = 'edit';
            $projects = $proj->all();
        } else {
            $project = $task->project();
        }

        $querys = $this->request->all();
        legal_or($querys, [
            'trending' => ['in:asc,desc', 'asc']
        ]);

        $trendings = $trending
        ->where([
            'ref_type' => 'task',
            'ref_id'   => $task->id,
        ])
        ->sort([
            'at' => $querys['trending']
        ])
        ->get();
        
        view("ldtdf/task/{$action}")
        ->withTaskProjectsProjectTrendings(
            $task,
            $projects,
            $project,
            $trendings
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
