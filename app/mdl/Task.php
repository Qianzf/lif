<?php

namespace Lif\Mdl;

use Lif\Job\{DeployTask, SendMailWhenTaskAssign};

class Task extends Mdl
{
    use \Lif\Traits\TaskStatus;

    protected $table = 'task';

    protected $rules = [
        'creator'  => 'int|min:1',
        'story'    => 'int|min:1',
        'project'  => 'int|min:1',
        'current'  => 'int|min:1',
        'status'   => 'string',
        'notes'    => 'string',
        'branch'   => 'string',
        'env'      => 'string',
        'manually' => 'ciin:yes,no',
        'config'   => 'json',
        'deploy'   => 'string',
        'origin_type' => 'ciin:story,bug',
        'origin_id'   => 'int|min:1',
    ];

    public function searchOriginIdsByTitle(
        string $origin,
        string $keyword
    )
    {
        $ids = db()
        ->table($origin)
        ->select('id')
        ->whereTitle('like', "%{$keyword}%")
        ->get();

        return $ids ? array_column($ids, 'id') : [];
    }

    public function getAllProjects()
    {
        return db()
        ->table('project')
        ->select('name', 'id')
        ->get();
    }

    public function isForWeb()
    {
        return (
            ($project = $this->project())
            && ('web' == strtolower($project->type))
        );
    }

    public function deployable()
    {
        if ($this->alive() && in_array(strtolower($this->status), [
            'deving',
            'waitting_dev',
            'waitting_confirm_env',
            'fixing_test',
            'waitting_dep2test',
            'waitting_fix_test',
        ])) {
            if (($project = $this->project())->alive()) {
                return $project->deployable();
            }
        }

        return false;
    }

    public function environment(array $fwhere = [])
    {
        return $this->belongsTo([
            'model' => Environment::class,
            'lk' => 'env',
            'fk' => 'id',
            'fwhere' => $fwhere,
        ]);
    }

    public function current(string $key = null)
    {
        if ($current = $this->belongsTo(
            User::class,
            'current',
            'id'
        )) {
            return $key ? $current->$key : $current;
        }
    }

    private function enqueueTaskJobs(bool $deploy = false)
    {
        if ($this->alive()) {
            if ($deploy) {
                enqueue(
                    (new DeployTask)->setTask($this->id)
                )
                ->on('task_deploy')
                ->try(3)
                ->timeout(10);
            }

            if (($current = $this->current()) && $current->alive()) {
                enqueue(
                    (new SendMailWhenTaskAssign)->setTask($this->id)
                )
                ->on('mail_send')
                ->try(3)
                ->timeout(3);
            }
        }

        return true;
    }

    // !!! $params should be validated before
    public function assign(array $params)
    {
        db()->start();
        
        $this->last    = $params['assign_from'];
        $this->current = $params['assign_to'];
        $this->status  = strtolower($params['action']);
        $notes         = ($params['assign_notes'] ?? null);

        if ($deploy = (
            in_array($this->status, [
                'waitting_dep2test',
                'waitting_dep2stage',
                'waitting_dep2stablerc',
                'waitting_dep2prod',
            ]) && (
                ($next = model(User::class, $params['assign_to']))
                && $next->alive()
                && ('ops' == strtolower($next->role))
            )
        )) {
            if (('yes' == ($this->manually = ($params['manually'] ?? 'no')))) {
                $this->deploy = $notes;
            }

            if ($config = ($params['config'] ?? null)) {
                $this->config = $config;
            }

            if ($branch = ($this->getDefaultBranch($params['branch'] ?? null))) {
                $this->branch = $branch;
            }
        }

        if (($this->save() >= 0)
            && $this->enqueueTaskJobs($deploy)
            && ($this->addTrending(
                'assign',
                $params['assign_from'],
                $params['assign_to'],
                $notes
            ) > 0)
        ) {
            db()->commit();

            return true;
        }

        db()->rollback();

        return false;
    }

    public function getDefaultBranch(string $branch = null)
    {
        if ($this->isForWeb()) {
            if ($branch = trim($branch)) {
                return $branch;
            }

            if ($this->alive()) {
                $flag = substr($this->origin_type, 0, 1);

                return "{$flag}{$this->origin_id}t{$this->id}";
            }
        }

        return null;
    }

    // What actions can given user role do
    public function getActionsOfRole($user = null)
    {
        if (is_null($user) || (!ispint($user) && !is_object($user))) {
            return array_column(
                db()
                ->table('task_status')
                ->select('`key`')
                ->where('assignable', 'yes')
                ->get(),
                'key'
            );
        }

        $user    = ispint($user) ? model(User::class, $user) : $user;
        $role    = ucfirst($user->role);
        $handler = "getActionsOfRole{$role}";
        
        return $this->$handler();
    }

    public function getAssignableUsers(array $where = [], int $self)
    {
        $status = underline2camelcase(strtolower($this->status));
        $taskStatusHandler = "getAssignableUsersWhen{$status}";

        $query = db()
        ->table('user')
        ->select('id', 'name', 'role')
        ->whereId('!=', $self)
        ->whereRole('!=', 'admin');

        if ($where) {
            $query = $query->where($where);
        }

        if (method_exists($this, $taskStatusHandler)) {
            $query = $this->$taskStatusHandler($query);
        }

        $users = $query->get();

        array_walk($users, function (&$item) {
            $item['name'] = $item['name']
            .'( '
            .L("ROLE_{$item['role']}")
            .' )'
            ;
            unset($item['role']);
        });

        return $users;
    }

    public function hasConflictTask(
        int $project,
        int $origin = null,
        string $type = null
    )
    {
        $type = $type ?? ($this->origin_type ?: 'story');
        
        if ($origin = ($origin ?? $this->$type()->id)) {
            return $this
            ->whereProject($project)
            ->where([
                'origin_type' => $type,
                'origin_id' => $origin,
            ])
            ->get();
        }

        excp('No story parent to relate.');
    }

    public function relateTasks(int $origin = null, string $type = null)
    {
        $type = $type ?? $this->origin_type;
        
        if ($origin = ($origin ?? ($this->$type()->id ?? null))) {
            return $this
            ->whereId('!=', $this->id)
            ->where([
                'origin_type' => $type,
                'origin_id' => $origin,
            ])
            ->get();
        }

        // excp('No story parent to relate.');
    }

    public function getAssignableStatuses()
    {
        $status  = underline2camelcase(strtolower($this->status));
        $handler = "getAssignableStatusesWhen{$status}";

        $taskStatusHandler = method_exists($this, $handler)
        ? $handler : 'getAllStatus';

        return $this->$taskStatusHandler();
    }

    public function canBeEditedBY(int $user)
    {
        if ($user) {
            return (
                ($this->creator == $user)
                && (strtolower($this->status) == 'activated')
            );
        }

        return false;
    }

    public function activate(int $user)
    {
        if ($this->canBeActivatedBy($user)) {
            $this->status = 'activated';

            return ($this->save() >= 0);
        }

        return false;
    }

    public function cancel(int $user)
    {
        if ($this->canBeCanceledBy($user)) {
            $this->status = 'canceled';

            return ($this->save() >= 0);
        }

        return false;
    }

    public function confirm(int $user)
    {
        if ($this->canBeConfirmedBY($user)) {
            $role = ucfirst(strtolower($this->current()->role));
            if ($status = $this->getStatusConfirmed()) {
                $this->status = $status;

                if ($status == 'FINISHED') {
                    $this->current = 0;
                }

                return ($this->save() >= 0);
            }

            return true;
        }

        return false;
    }

    public function getStatusList(string $assignable = 'yes')
    {
        $status = db()
        ->table('task_status')
        ->select('`key`')
        ->whereAssignable($assignable)
        ->get();

        return array_column($status, 'key');
    }

    public function canBeActivatedBy(int $user)
    {
        if ($user) {
            $status = in_array(strtolower($this->status), [
                'online',
                'finished',
                'canceled',
            ]);

            return (($this->creator()->id == $user) && $status);
        }

        return false;
    }

    public function canBeCanceledBy(int $user)
    {
        if ($user) {
            return (
                $this->statusIsOperable() && ($this->creator()->id == $user)
            );
        }

        return false;
    }

    public function canBeConfirmedBY(int $user)
    {
        if ($user) {
            $status = strtolower($this->status);
            if ($status == 'activated') {
                return (strtolower($this->current()->role) == 'dev');
            }
            if (in_array($status, [
                'waitting_regression',
                'finished',
            ])) {
                return false;
            }
            if (('online' == $status)
                || !in_array($status, $this->getStatusList('no'))
            ) {
                return ($user == $this->current);
            }

            return false;
        }

        excp('Missing user id.');
    }

    public function canAssignTo(string $status, int $user)
    {
        if (($user = model(User::class, $user)) && $user->alive()) {
            if (('dev' == strtolower($user->role)) && (! $this->isForWeb())) {
                return true;
            }

            return in_array(
                strtoupper($status),
                $this->getActionsOfRole($user)
            );
        }

        return false;
    }

    public function canBeAssignedBy(int $user)
    {
        if ($user) {
            return (
                $this->statusIsOperable() && ($user == $this->current)
            );
        }

        excp('Missing user id.');
    }

    public function statusIsOperable()
    {
        return !in_array(
            strtolower($this->status),
            $this->getUnoperatableStatus()
        );
    }

    public function trendings(array $querys = [])
    {
        $relationship = [
            'model' => Trending::class,
            'lk' => 'id',
            'fk' => 'ref_id',
            'fwhere' => [
                'ref_type' => 'task',
            ],
        ];

        if ($order = ($querys['trending'] ?? 'desc')) {
            $relationship['sort'] = [
                'id' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

    public function title()
    {
        $origin = $this->origin();
        $type   = ('story' == $this->origin_type) ? 'S' : 'B';

        return "{$origin->title} ({$type}{$origin->id}/T{$this->id})";
    }

    public function origin(string $key = null, string $type = null)
    {
        if ($type = ($type ?? ($this->origin_type ?: 'story'))) {
            $class = ('story' == $type) ? Story::class : Bug::class;

            if ($origin = $this->belongsTo([
                'model' => $class,
                'lk' => 'origin_id',
                'fk' => 'id',
                'lwhere' => [
                    'origin_type' => $type,
                ],
            ])) {
                return $key ? $origin->$key : $origin;
            }
        }
    }

    public function bug(string $key = null)
    {
        return $this->origin($key, 'bug');
    }

    public function story(string $key = null)
    {
        return $this->origin($key, 'story');
    }

    public function project(string $attr = null)
    {
        $project = $this->belongsTo(
            Project::class,
            'project',
            'id'
        );

        if (! $project) {
            excp(L('MISSING_TASK_RELATED_PROJECT'));
        }

        return $attr ? $project->$attr : $project;
    }

    public function creator(string $key = null)
    {
        $creator = $this->belongsTo(
            User::class,
            'creator',
            'id'
        );

        if (! $creator) {
            excp(L('MISSING_TASK_CREATOR'));
        }

        return $key ? $creator->$key : $creator;
    }

    public function addTrending(
        string $action,
        int $user,
        int $target = null,
        string $notes = null
    )
    {
        return db()->table('trending')->insert([
            'at'        => date('Y-m-d H:i:s'),
            'user'      => $user,
            'action'    => $action,
            'ref_state' => $this->status,
            'ref_type'  => 'task',
            'ref_id'    => $this->id,
            'target'    => $target,
            'notes'     => $notes,
        ]);
    }
}
