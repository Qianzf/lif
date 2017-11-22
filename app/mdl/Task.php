<?php

namespace Lif\Mdl;

class Task extends Mdl
{
    protected $table = 'task';

    protected $rules = [
        'custom'  => ['in:yes,no', 'no'],
        'creator' => 'int|min:1',
        'title'   => 'string',
        'status'  => 'string',
        'url'     => 'when:custom=no|url',
        'story_role'     => 'when:custom=yes|string',
        'story_activity' => 'when:custom=yes|string',
        'story_value'    => 'when:custom=yes|string',
        'acceptances'    => 'when:custom=yes|string',
        'extra'          => 'when:custom=yes|string',
    ];

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'creator',
            'id'
        );
    }

    public function addTrending(string $event)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'uid'    => share('user.id'),
            'event'  => strtolower($event).'_task',
            'ref_id' => $this->id,
        ]);
    }
}
