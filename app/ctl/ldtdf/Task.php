<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;
use Lif\Mdl\{User, Project, Story};

class Task extends Ctl
{
    public function getAttachableStories(Story $story)
    {
        $where = [];
        
        if ($search = $this->request->get('search')) {
            $where[] = ['title', 'like', "%{$search}%"];
        }

        return response($story->list(['id', 'title'], $where, false));
    }

    public function getAssignableUsers(TaskModel $task)
    {
        if (! $task->isAlive()) {
            return response([
                'err' => '403',
                'msg' => L('NO_TASK'),
            ]);
        }

        $where = [];
        
        if ($search = $this->request->get('search')) {
            $where[] = ['name', 'like', "%{$search}%"];
        }

        return response($task->getAssignableUsers($where));    
    }

    public function activate(TaskModel $task)
    {
        return $this->updateStatus(
            'ACTIVATE', 
            $task,
            $this->request->get('activate_reason'),
            function () use ($task) {
                $task->current = share('user.id');
                return ($task->save() >= 0);
            }
        );
    }

    public function cancel(TaskModel $task)
    {
        return $this->updateStatus(
            'CANCEL',
            $task,
            $this->request->get('cancel_reason'),
            function () use ($task) {
                $task->branch = $task->current = null;
                return ($task->save() >= 0);
            }
        );
    }

    public function confirm(TaskModel $task)
    {
        return $this->updateStatus('CONFIRM', $task);
    }

    private function updateStatus(
        string $action,
        TaskModel $task,
        string $notes = null,
        \Closure $callback = null
    )
    {
        $msg = "{$action}_PERMISSION_DENIED";

        if (call_user_func([$task, strtolower($action)], share('user.id'))) {
            $msg = "{$action}_OK";

            $task->addTrending($action, null, $notes);

            if ($callback) {
                $callback();
            }
        }

        share_error_i18n($msg);

        return redirect("/dep/tasks/{$task->id}");
    }

    public function assign(TaskModel $task)
    {
        return redirect("/dep/tasks/{$task->id}");
    }

    public function assignTo(TaskModel $task)
    {
        if (! $task->isAlive()) {
            share_error_i18n('NO_TASK');
            return redirect($this->route);
        }

        if (! $task->canBeAssignedBy()) {
            share_error_i18n('ASSIGN_PERMISSION_DENIED');
            return redirect($this->route);
        }

        $data = $this->request->posts();
        
        if (true !== ($err = validate($data, [
            'assign_to' => 'need|int|min:1',
            'action'    => 'need|string|notin:0',
            'branch'    => 'when:action=WAITTING_DEP2TEST|need|string|notin:0',
            'manually'  => 'when:action=WAITTING_DEP2TEST|need|ciin:yes,no',
            'assign_notes' => 'string',
        ])) || !in_array(
            $data['action'],
            $task->getActionsOfRole($data['assign_to'])
        )) {
            share_error_i18n(
                (true === $err) ? 'CANNT_ASSIGN_ACTION2ROLE' : $err
            );

            return redirect($this->route);
        }

        share_error_i18n(
            $task->assign($data) ? 'ASSIGN_OK' : 'ASSIGN_FAILED'
        );

        return redirect($this->route);
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
            $user->list()
        );
    }

    public function add(
        TaskModel $task,
        Story $story,
        Project $project
    ) {
        $data = $this->request->all();

        legal_or($data, [
            'story' => ['int|min:1', null],
        ]);

        if ($sid = $data['story']) {
            if (! $story->find($sid)) {
                shares([
                    '__error'   => L('STORY_NOT_FOUND', $sid),
                    'back2last' => share('url_previous'),
                ]);

                return redirect($this->route);
            }
        }

        share('hide-search-bar', true);
        view('ldtdf/task/edit')
        ->withTaskStoryProjectProjectsEditableTrendings(
            $task,
            $story,
            $project,
            $project->all(),
            true,
            $task->trendings()
        );
    }

    public function edit(TaskModel $task)
    {
        if (! $task->isAlive()) {
            share_error_i18n('NO_TASK');
            return redirect(share('url_previous'));
        }

        if (! ($story = $task->story())) {
            share_error_i18n('NO_STORY');
            return redirect(share('url_previous'));   
        }

        if (! ($project = $task->project())) {
            share_error_i18n('NO_PROJECT');
            return redirect(share('url_previous'));   
        }

        return $this->add($task, $story, $project);
    }

    public function info(TaskModel $task)
    {
        if (! $task->isAlive()) {
            share_error_i18n('NO_TASK');
            return redirect(share('url_previous'));
        }

        $querys = $this->request->gets;
        legal_or($querys, [
            'trending' => ['in:asc,desc', 'desc']
        ]);

        $user        = share('user.id');
        $activeable  = ($task->canBeActivatedBy($user));
        $cancelable  = ($task->canBeCanceledBy($user));
        $confirmable = ($task->canBeConfirmedBY($user));
        $editable    = ($task->canBeEditedBY());
        $assignable  = ($task->canBeAssignedBy($user));

        share('hide-search-bar', true);

        view('ldtdf/task/info')
        ->withTaskStoryTasksProjectTrendingsActiveableCancelableConfirmableEditableAssignableAssigns(
            $task,
            $task->story(),
            $task->relateTasks(),
            $task->project(),
            $task->trendings($querys),
            $activeable,
            $cancelable,
            $confirmable,
            $editable,
            $assignable,
            $task->assigns()
        );
    }

    public function create(TaskModel $task)
    {
        $data = $this->validate($this->request->all(), [
            'story'   => 'int|min:1',
            'project' => 'int|min:1',
        ]);
        if ($task->hasConflictTask(intval($data['project']), intval($data['story']))) {
            share_error_i18n('PROJECT_EXIST_IN_STORY');
            return redirect($this->route);
        }

        $data['status']  = 'activated';
        $data['creator'] = $data['current'] = share('user.id');

        if (($status = $task->create($data))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
            $task->addTrending('create');
        } else {
            $msg    = L('CREATED_FAILED', L($status));
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

        $err = is_integer($err) ? null : L($err);

        share_error(L($status, $err));

        redirect("{$this->route}/edit");
    }
}
