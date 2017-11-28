<?php

namespace Lif\Mdl;

class Task extends Mdl
{
    protected $table = 'task';

    protected $rules = [
        'creator' => 'int|min:1',
        'project' => 'int|min:1',
        'status'  => 'string',
        'title'   => 'string',
    ];

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

    public function canBeAssignedBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
            return true;
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

        if ($order = ($querys['trending'] ?? null)) {
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

    public function addTrending(string $action)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'user'   => share('user.id'),
            'action' => $action,
            'ref_type' => 'task',
            'ref_id'   => $this->id,
        ]);
    }
}
