<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Task as TaskModel;
use Lif\Mdl\{User, Project, Story, Bug};

class Task extends Ctl
{
    public function getAttachableStories(Story $story)
    {
        return $this->searchOriginType($story);
    }

    public function getAttachableBugs(Bug $bug)
    {
        return $this->searchOriginType($bug);
    }

    private function searchOriginType($origin)
    {
        $where = [];
        
        if ($search = $this->request->get('search')) {
            $where[] = ['title', 'like', "%{$search}%"];
        }

        return response($origin->list(['id', 'title'], $where, false));
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
            'assign_notes' => 'when:manually=yes|string',
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
        Project $project,
        Story $story,
        Bug $bug
    ) {
        $data = $this->request->all();

        legal_or($data, [
            'story' => ['int|min:1', null],
            'bug'   => ['int|min:1', null],
        ]);

        $trendings = null;

        if ($task->isAlive()) {
            $project = $task->project();
            $story   = $task->story();
            $bug     = $task->bug();
            $trendings = $task->trendings();
        } else {
            $error = false;
            if (($sid = $data['story']) && (! $story->find($sid))) {
                $error = L('STORY_NOT_FOUND', $sid);
            }
            if (($bid = $data['bug']) && (! $bug->find($bid))) {
                $error = L('BUG_NOT_FOUND', $bid);
            }

            if (false !== $error) {
                shares([
                    '__error'   => $error,
                    'back2last' => share('url_previous'),
                ]);

                return redirect($this->route);
            }
        }

        view('ldtdf/task/edit')
        ->withTaskStoryBugProjectProjectsEditableTrendings(
            $task,
            ($story ?? model(Story::class)),
            ($bug   ?? model(Bug::class)),
            $project,
            $project->all(true, false),
            true,
            $trendings
        )
        ->share('hide-search-bar', true);
    }

    public function edit(TaskModel $task)
    {
        if (! $task->isAlive()) {
            share_error_i18n('NO_TASK');
            return redirect(share('url_previous'));
        }

        $origin = strtolower($task->origin_type);

        if ((!($story = $task->story())) && ($origin == 'story')) {
            share_error_i18n('NO_STORY');
            return redirect(share('url_previous'));
        }

        if ((!($bug = $task->bug())) && ($origin == 'bug')) {
            share_error_i18n('NO_BUG');
            return redirect(share('url_previous'));
        }

        if (! ($project = $task->project())) {
            share_error_i18n('NO_PROJECT');
            return redirect(share('url_previous'));
        }

        return $this->add(
            $task,
            $project,
            ($story ?? model(Story::class)),
            ($bug   ?? model(Bug::class))
        );
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
        $story = $bug = null;
        if ('story' == strtolower($task->origin_type)) {
            if (! ($story = $task->story())) {
                share_error_i18n('NO_STORY');
                return redirect(share('url_previous'));
            }
        } else {
            if (! ($bug = $task->bug())) {
                share_error_i18n('NO_BUG');
                return redirect(share('url_previous'));
            }
        }

        share('hide-search-bar', true);

        view('ldtdf/task/info')
        ->withOriginTaskBugStoryTasksProjectTrendingsActiveableCancelableConfirmableEditableAssignableAssigns(
            $task->origin(),
            $task,
            $bug,
            $story,
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
            'origin_type' => 'need|ciin:story,bug',
            'origin_id' => 'need|int|min:1',
            'project' => 'int|min:1',
        ]);

        if ($task->hasConflictTask(
            intval($data['project']),
            intval($data['origin_id']),
            trim($data['origin_type'])
        )) {
            share_error_i18n("PROJECT_EXIST_IN_{$data['origin_type']}");
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
        $data = $this->request->posts;

        $this->validate($data, [
            'origin_type' => 'need|ciin:bug,story',
            'origin_id' => 'need|int|min:1',
            'project' => 'need|int|min:1',
            'notes' => 'string',
        ]);

        if ($task->creator != share('user.id')) {
            share_error_i18n('UPDATE_PERMISSION_DENIED');
            return redirect($this->route);
        }

        if (! $task->isAlive()) {
            share_error_i18n('TASK_NOT_FOUND');
            return redirect('/dep/tasks');
        }
        if (('bug' == $data['origin_type']) && (! $task->bug())) {
            share_error_i18n('NO_BUG');
            return redirect($this->route);
        } elseif (('story' == $data['origin_type']) && (! $task->story())) {
            share_error_i18n('NO_STORY');
            return redirect($this->route);
        }

        if (!empty_safe($err = $task->save($data, false))
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
