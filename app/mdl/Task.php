<?php

namespace Lif\Mdl;

class Task extends Mdl
{
    use \Lif\Traits\TaskStatus;

    protected $table = 'task';

    protected $rules = [
        'creator' => 'int|min:1',
        'story'   => 'int|min:1',
        'project' => 'int|min:1',
        'status'  => 'string',
        'title'   => 'string',
    ];

    public function assign(
        int $user,
        string $status,
        string $notes = null
    )
    {
        db()->start();
        
        $this->status  = $status;
        $this->current = $user;

        if (($this->save() >= 0)
            && ($this->addTrending('assign', $user, $notes) > 0)
        ) {
            db()->commit();

            return true;
        }

        db()->rollback();

        return false;
    }

    public function getActionString()
    {
        return implode(',', array_column(
            db()
            ->table('task_status')
            ->select('`key`')
            ->where('assignable', 'yes')
            ->get(),
            'key'
        ));
    }

    public function getAssignableUsers(array $where = [])
    {
        $status = underline2camelcase(strtolower($this->status));
        $taskStatusHandler = "getAssignableUsersWhen{$status}";

        $query = db()
        ->table('user')
        ->select('id', 'name', 'role');

        if ($where) {
            $query = $query->where($where);
        }

        $users = $this->$taskStatusHandler($query);

        array_walk($users, function (&$item) {
            $item['name'] = $item['name']
            .'( '
            .lang("ROLE_{$item['role']}")
            .' )'
            ;
            unset($item['role']);
        });

        return $users;
    }

    public function hasConflictTask(int $project, int $story = null)
    {
        if ($story = ($story ?? $this->story()->id)) {
            return $this
            ->whereProjectStory($project, $story)
            ->get();
        }

        excp('No story parent to relate.');
    }

    public function relateTasks(int $story = null)
    {
        if ($story = ($story ?? $this->story()->id)) {
            return $this
            ->whereStory($story)
            ->whereId('!=', $this->id)
            ->get();
        }

        excp('No story parent to relate.');
    }

    public function assigns()
    {
        $status = underline2camelcase(strtolower($this->status));
        $taskStatusHandler = "getAssignActionsWhen{$status}";

        return $this->$taskStatusHandler();
    }

    public function canEdit()
    {
        return (
            ($this->creator == share('user.id'))
            && (strtoupper($this->status) == 'CREATED')
        );
    }

    public function canBeAssignedBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
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
            'where' => [
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

    public function story()
    {
        return $this->belongsTo(
            Story::class,
            'story',
            'id'
        );
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
            'at'       => date('Y-m-d H:i:s'),
            'user'     => share('user.id'),
            'action'   => $action,
            'ref_type' => 'task',
            'ref_id'   => $this->id,
            'target'   => $target,
            'notes'    => $notes,
        ]);
    }
}
