<?php

namespace Lif\Mdl;

use Lif\Job\{DeployTask, SendMailWhenTaskAssign};

class Task extends Mdl
{
    use \Lif\Traits\TaskStatus;

    protected $table = 'task';

    protected $rules = [
        'creator'  => 'int|min:1',
        'project'  => 'int|min:0',
        'current'  => 'int|min:0',
        'status'   => 'string',
        'notes'    => 'string',
        'branch'   => 'string',
        'env'      => 'string',
        'manually' => 'ciin:yes,no',
        'config'   => 'json',
        'deploy'   => 'string',
        'origin_type' => 'ciin:story,bug',
        'origin_id'   => 'int|min:1',
        'first_dev'   => 'int|min:0',
    ];

    public function getOriginsByPriority(int $priority)
    {
        $this->getTasksByOrigins(
            $this->getStoryIdsByPriority($priority),
            $this->getBugIdsByPriority($priority)
        );
    }

    public function getOriginsByProduct(int $product)
    {
        $this->getTasksByOrigins(
            $this->getStoryIdsByProduct($product),
            $this->getBugIdsByProduct($product)
        );
    }

    private function getTasksByOrigins(array $stories = [], array $bugs = [])
    {
        $native = '';
        
        if ($stories) {
            $stories = implode(',', $stories);
            $native .= <<< SQL
(`origin_type` = 'story' and `origin_id` in ({$stories}))
SQL;
        }

        if ($bugs) {
            $bugs = implode(',', $bugs);
            $native .= $stories ? ' or ' : '';
            $native .= <<< SQL
(`origin_type` = 'bug' and `origin_id` in ({$bugs}))
SQL;
        }

        if ($native) {
            $this->appendWhere(" ({$native}) ");

            return true;
        }

        return false;
    }

    public function searchOriginsByTitle($search)
    {
        if (! $search) {
            return null;
        }

        if (false === $this->getTasksByOrigins(
            $this->searchOriginIdsByTitle('story', $search),
            $this->searchOriginIdsByTitle('bug', $search)
        )) {
            $this->whereId('<', 0);

            return false;
        }
    }

    public function getBugIdsByFilter(string $key, $val)
    {
        return $this->getOriginIdsByFilter('bug', $key, $val);
    }

    public function getBugIdsByPriority(int $priority)
    {
        return $this->getBugIdsByFilter('priority', $priority);
    }

    public function getBugIdsByProduct(int $product)
    {
        return $this->getBugIdsByFilter('product', $product);
    }

    public function getStoryIdsByPriority(int $priority)
    {
        return $this->getStoryIdsByFilter('priority', $priority);
    }

    public function getStoryIdsByProduct(int $product)
    {
        return $this->getStoryIdsByFilter('product', $product);
    }

    public function getStoryIdsByFilter(string $key, $val)
    {
        return $this->getOriginIdsByFilter('story', $key, $val);
    }

    public function getOriginIdsByFilter(string $origin, string $key, $val)
    {
        $origins = db()
        ->table($origin)
        ->select('id')
        ->where($key, $val)
        ->get();

        if ($origins && is_array($origins)) {
            $origins = array_column($origins, 'id');
        }

        return $origins;
    }

    public function getProjects()
    {
        return db()
        ->table('project')
        ->select('id', 'name', 'url', 'type')
        ->get();
    }

    public function getDevelopers()
    {
        return db()
        ->table('user')
        ->select('id', 'name', 'ability', 'role')
        ->whereStatusRole(1, 'dev')
        ->get();
    }

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
            ($project = $this->project(null, false))
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
            if (true
                && ($project = $this->project(null, false))
                && $project->alive()
            ) {
                return $project->deployable();
            }
        }

        return false;
    }

    public function environment(array $fwhere = [], string $key = null)
    {
        if ($env = $this->belongsTo([
            'model' => Environment::class,
            'lk' => 'env',
            'fk' => 'id',
            'fwhere' => $fwhere,
        ])) {
            return $key ? $env->$key : $env; 
        }
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
                ->timeout(600);    // 10 minutes
            }

            if (($current = $this->current()) && $current->alive()) {
                enqueue(
                    (new SendMailWhenTaskAssign)->setTask($this->id)
                )
                ->on('mail_send')
                ->try(3)
                ->timeout(300);    // 5 minutes
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
            if (ci_equal('yes', ($params['dependency'] ?? null))) {
                if ('yes' == ($params['manually'] ?? 'no')) {
                    $this->manually = 'yes';
                    $this->deploy   = $notes;
                }

                if (! $this->branch) {
                    $this->branch = $this->getDefaultBranch(
                        $params['branch'] ?? null
                    );
                }

                if ($config = ($params['config'] ?? null)) {
                    $this->config = $config;
                }
            }
        }

        if (ispint($this->save())
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

        $user    = ispint($user, false) ? model(User::class, $user) : $user;
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

    public function canBeUpdatedBy(int $user)
    {
        return (true
            && ($env = $this->environment())
            && ($env->alive())
            && ci_equal($env->status, 'locked')
            && ($user = model(User::class, $user))
            && ($user->alive())
            && ci_equal($user->role, 'dev')
        );
    }

    public function canBeEditedBy(int $user)
    {
        if ($user) {
            return in_array($user, [
                $this->creator,
                $this->first_dev,
            ]) || (true
                && ci_equal($this->status, 'waiting_edit')
                && ($user == $this->current)
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
        if ($this->canBeConfirmedBy($user)) {
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

    public function canBeTestedBy(int $user)
    {
        if ($current = $this->current()) {
            return (
                ($current->id == $user)
                && (strtolower($current->role) == 'test')
            );
        }

        return false;
    }
    
    public function canBeConfirmedBy(int $user)
    {
        if ($user) {
            $status = strtolower($this->status);
            if ('activated' == $status) {
                return (true
                    && ($user == $this->creator)
                    && ($user = model(User::class, $user))
                    && $user->alive()
                    && ci_equal('dev', $user->role)
                );
            }

            if (in_array($status, [
                'waitting_regression',
                'waiting_edit',
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

    public function getTrendingCount()
    {
        return db()
        ->table('trending')
        ->where([
            'ref_type' => 'task',
            'ref_id'   => $this->id,
        ])
        ->count();
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
            'from' => ($querys['from'] ?? 0),
            'take' => ($querys['take'] ?? 0),
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

    public function product(string $key = null)
    {
        if (true
            && ($origin = $this->origin())
            && ($product = $origin->product($key))
        ) {
            return $product;
        }
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

    public function project(string $attr = null, bool $excp = true)
    {
        $project = $this->belongsTo(
            Project::class,
            'project',
            'id'
        );

        if (! $project) {
            return $excp ? excp(L('MISSING_TASK_RELATED_PROJECT')) : null;
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

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }
}
