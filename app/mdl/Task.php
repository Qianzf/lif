<?php

namespace Lif\Mdl;

class Task extends Mdl
{
    protected $table = 'task';

    protected $rules = [
        'custom'  => ['need|in:yes,no', 'no'],
        'creator' => 'need|int|min:1',
        'title'   => 'need|string',
        'status'  => 'string',
        'url'     => 'when:custom=no|url',
        'story_role'     => 'when:custom=yes|string',
        'story_activity' => 'when:custom=yes|string',
        'story_value'    => 'when:custom=yes|string',
    ];
}
