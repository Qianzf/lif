<?php

namespace Lif\Mdl;

class Task extends Mdl
{
    protected $table = 'task';

    protected $rules = [
        'custom'  => ['in:yes,no', 'no'],
        'creator' => 'int|min:1',
        'project' => 'int|min:1',
        'title'   => 'string',
        'status'  => 'string',
        'url'     => 'when:custom=no|need|url',
        'story_role'     => 'when:custom=yes|need|string',
        'story_activity' => 'when:custom=yes|need|string',
        'story_value'    => 'when:custom=yes|need|string',
        'acceptances'    => 'when:custom=yes|need|string',
        'extra'          => 'when:custom=yes|string',
    ];

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
