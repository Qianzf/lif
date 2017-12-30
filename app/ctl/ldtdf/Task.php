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
        if (! $task->alive()) {
            return response([
                'err' => '403',
                'msg' => L('NO_TASK'),
            ]);
        }

        $where = [];
        
        if ($search = $this->request->get('search')) {
            $where[] = ['name', 'like', "%{$search}%"];
        }

        return response($task->getAssignableUsers(
            $where,
            share('user.id')
        ));
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
        $msg  = "{$action}_PERMISSION_DENIED";
        $user = share('user.id');

        if (call_user_func([$task, strtolower($action)], $user)) {
            $msg = "{$action}_OK";

            $task->addTrending($action, $user, null, $notes);

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
        if (! $task->alive()) {
            share_error_i18n('NO_TASK');
            return redirect($this->route);
        }

        $user = share('user.id');

        if (! $task->canBeAssignedBy($user)) {
            share_error_i18n('ASSIGN_PERMISSION_DENIED');
            return redirect($this->route);
        }

        $data = $this->request->posts();
        $data['assign_from'] = $user;
        
        if (true !== ($err = validate($data, [
            'assign_from'  => 'need|int|min:1',
            'assign_to'    => 'need|int|min:1',
            'action'       => 'need|string|notin:0',
            'dependency'   => ['ciin:yes,no', 'no'],
            'config'       => 'string', 
            'branch'       => 'when:dependency=yes|string|notin:0',
            'manually'     => 'when:dependency=yes|need|ciin:yes,no',
            'assign_notes' => 'when:manually=yes|string',
        ])) || !$task->canAssignTo($data['action'], $data['assign_to'])) {
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

    public function todo(TaskModel $task, User $user)
    {
        if (! ($user = $user->find(share('user.id')))) {
            share_error_i18n('NO_USER');

            return redirect($this->route);
        }

        $task->current = $user->id;

        return $this->index($task, $user);
    }

    public function index(TaskModel $task, User $user)
    {
        $querys = $this->request->gets();

        legal_or($querys, [
            'origin'   => ['ciin:story,bug', null],
            'project'  => ['int|min:1', null],
            'creator'  => ['int|min:1', null],
            'search'   => ['string', null],
            'position' => ['int|min:1', null],
            'status'   => ['string|notin:-1', null],
            'sort'     => ['ciin:asc,desc', 'desc'],
        ]);

        $displayPosition = $displayMenu = true;
        $hasSearchResult = $tasks = false;

        if ($user->alive()) {
            $task->whereCurrent($user->id);
            $displayPosition = $displayMenu =false;
        }

        if ($search = $querys['search']) {
            // TODO
            // fulltext search
            if ($bugs = $task->searchOriginIdsByTitle('bug', $search)) {
                $hasSearchResult = true;
                $task->or([
                    ['origin_type' => 'bug'],
                    ['origin_id'   => $bugs],
                ]);
            }
            if ($stories = $task->searchOriginIdsByTitle('story', $search)) {
                $hasSearchResult = true;
                $task->or([
                    ['origin_type' => 'story'],
                    ['origin_id'   => $stories],
                ]);
            }
        } else {
            $hasSearchResult = true;
        }

        if ($hasSearchResult) {
            if ($origin = $querys['origin']) {
                $task->where('origin_type', strtolower($origin));
            }
            if ($project = $querys['project']) {
                $task->whereProject($project);
            }
            if ($creator = $querys['creator']) {
                $task->whereCreator($creator);
            }
            if ($current = $querys['position']) {
                $task->whereCurrent($current);
            }
            if ($status = $querys['status']) {
                $task->where(
                    db()->native('LOWER(`status`)'),
                    strtolower($status)
                );
            }

            $tasks = $task
            ->sort([
                'create_at' => $querys['sort']
            ])
            ->get();
        }
        
        $users    = $user->list(['id', 'name'], null, false);
        $projects = $task->getAllProjects();

        return view('ldtdf/task/index')
        ->withStatusRecordsTasksProjectsUsersDisplaypositionDisplaymenu(
            $task->getAllStatus(),
            $task->count(),
            $tasks,
            array_combine(
                array_column($projects, 'id'),
                array_column($projects, 'name')
            ),
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),
            $displayPosition,
            $displayMenu
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

        if ($task->alive()) {
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
        if (! $task->alive()) {
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
        if (! $task->alive()) {
            share_error_i18n('NO_TASK');
            return redirect(share('url_previous'));
        }

        $querys = $this->request->gets();

        legal_or($querys, [
            'trending' => ['ciin:asc,desc', 'desc'],
        ]);

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

        $user        = share('user.id');
        $activeable  = $task->canBeActivatedBy($user);
        $cancelable  = $task->canBeCanceledBy($user);
        $confirmable = $task->canBeConfirmedBY($user);
        $editable    = $task->canBeEditedBY($user);
        $assignable  = $task->canBeAssignedBy($user);
        $untestable  = !$task->canBeTestedBy($user);
        $deployable  = $task->deployable();
        $acceptances = ($story && $story->alive())
        ? $story->getAcceptances()
        : null;

        view('ldtdf/task/info')
        ->withOriginTaskBugStoryAcceptancesTasksProjectTrendingsActiveableCancelableConfirmableEditableUntestableAssignableDeployableAssigns(
            $task->origin(),
            $task,
            $bug,
            $story,
            $acceptances,
            $task->relateTasks(),
            $task->project(),
            $task->trendings($querys),
            $activeable,
            $cancelable,
            $confirmable,
            $editable,
            $untestable,
            $assignable,
            $deployable,
            $task->getAssignableStatuses()
        )
        ->share('hide-search-bar', true);
    }

    public function create(TaskModel $task)
    {
        $user = share('user.id');

        $this->request
        ->setPost('status', 'activated')
        ->setPost('creator', $user)
        ->setPost('current', $user);

        return $this->responseOnCreated(
            $task,
            '/dep/tasks/?',
            function () use ($task) {
                if($task->hasConflictTask(
                    intval($this->request->get('project')),
                    intval($this->request->get('origin_id')),
                    ($type = trim($this->request->get('origin_type')))
                )) {
                    return "PROJECT_EXIST_IN_{$type}";
                }
            },
            function () use ($task, $user) {
                $task->addTrending('create', $user);
            }
        );
    }

    public function update(TaskModel $task)
    {
        return $this->responseOnUpdated(
            $task,
            '/dep/tasks',
            function () use ($task) {
                if ($task->creator != share('user.id')) {
                    return 'UPDATE_PERMISSION_DENIED';
                }

                $type = strtolower(
                    $this->request->get('origin_type')
                );

                if (('bug' == $type) && (! $task->bug())) {
                    return 'NO_BUG';
                }

                if (('story' == $type) && (! $task->story())) {
                    return 'NO_STORY';
                }
            },
            function () use ($task) {
                $task->addTrending('update', share('user.id'));
            }
        );
    }
}
