<?php

namespace Lif\Mdl;

use Lif\Job\Deploy;

class Task extends Mdl
{
    use \Lif\Traits\TaskStatus;
    use \Lif\Core\Traits\Queue;

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
        'deploy'   => 'string',
    ];

    public function current()
    {
        return $this->belongsTo(
            User::class,
            'current',
            'id'
        );
    }

    private function enqueueDeployTaskJob(bool $deploy = false)
    {
        if (! $deploy) {
            return true;
        }

        if ($this->isAlive()) {
            $this->prepare();

            $this
            ->enqueue(
                (new Deploy)->setTask($this->id)
            )
            ->on('deploy')
            ->try(3)
            ->timeout(30);
        }

        return true;
    }

    public function assign(array $params)
    {
        db()->start();
        
        $this->current = $params['assign_to'];
        $this->status  = strtolower($params['action']);
        $notes  = ($params['assign_notes'] ?? null);
        $deploy = in_array($this->status, [
            'waitting_dep2test',
            'waitting_update2test',
        ]);

        if (('yes' == ($this->manually = ($params['manually'] ?? 'no')))
            && $deploy
        ) {
            $this->deploy = $notes;
        }

        if ($branch = ($params['branch'] ?? null)) {
            $this->branch = $branch;
        }

        if (($this->save() >= 0)
            && $this->enqueueDeployTaskJob($deploy)
            && ($this->addTrending('assign', $this->current, $notes) > 0)
        ) {

            db()->commit();

            return true;
        }

        db()->rollback();

        return false;
    }

    // What actions can given user role do
    public function getActionsOfRole(int $user = null)
    {
        if (is_null($user)) {
            return array_column(
                db()
                ->table('task_status')
                ->select('`key`')
                ->where('assignable', 'yes')
                ->get(),
                'key'
            );
        }

        $role    = ucfirst(model(User::class, $user)->role);
        $handler = "getActionsOfRole{$role}";
        
        return $this->$handler();
    }

    public function getAssignableUsers(array $where = [])
    {
        $status = underline2camelcase(strtolower($this->status));
        $taskStatusHandler = "getAssignableUsersWhen{$status}";

        $query = db()
        ->table('user')
        ->select('id', 'name', 'role')
        ->whereId('!=', share('user.id'));

        if ($where) {
            $query = $query->where($where);
        }

        $users = $this->$taskStatusHandler($query);

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

    public function assigns()
    {
        $status = underline2camelcase(strtolower($this->status));
        $taskStatusHandler = "getAssignActionsWhen{$status}";

        return $this->$taskStatusHandler();
    }

    public function canBeEditedBY(int $user = null)
    {
        if ($user = ($user ?? share('user.id'))) {
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
            $confirmHandler  = "confirmWhen{$role}";
            if ($status = $this->$confirmHandler()) {
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

    public function canBeActivatedBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
            $status = in_array(strtolower($this->status), [
                'online',
                'finished',
                'canceled',
            ]);

            return (($this->creator()->id == $user) && $status);
        }

        return false;
    }

    public function canBeCanceledBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
            $status = !in_array(strtolower($this->status), [
                'canceled',
                'finished',
                'online',
            ]);

            return (($this->creator()->id == $user) && $status);
        }

        return false;
    }

    public function canBeConfirmedBY(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
            $status = strtolower($this->status);
            if ($status == 'activated') {
                return (strtolower($this->current()->role) == 'dev');
            }
            if ('finished' == $status) {
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

    public function canBeAssignedBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {

            if (in_array(strtolower($this->status), [
                'online',
                'finished',
            ])) {
                return false;
            }

            return ($user == $this->current);
        }

        excp('Missing user id.');
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
                'at' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

    public function bug()
    {
        return $this->origin('bug');
    }

    public function origin(string $type = null)
    {
        if ($type = ($type ?? ($this->origin_type ?: 'story'))) {
            $class = ('story' == $type) ? Story::class : Bug::class;

            return $this->belongsTo([
                'model' => $class,
                'lk' => 'origin_id',
                'fk' => 'id',
                'lwhere' => [
                    'origin_type' => $type,
                ],
            ]);
        }
    }

    public function story()
    {
        return $this->origin('story');
    }

    public function project()
    {
        return $this->belongsTo(
            Project::class,
            'project',
            'id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'creator',
            'id'
        );
    }

    public function addTrending(
        string $action,
        int $target = null,
        string $notes = null
    )
    {
        return db()->table('trending')->insert([
            'at'        => date('Y-m-d H:i:s'),
            'user'      => share('user.id'),
            'action'    => $action,
            'ref_state' => $this->status,
            'ref_type'  => 'task',
            'ref_id'    => $this->id,
            'target'    => $target,
            'notes'     => $notes,
        ]);
    }
}
