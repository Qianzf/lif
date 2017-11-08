<?php

namespace Lif\Mdl;

class Task extends Mdl
{
    protected $table = 'task';

    protected $rules = [
        'custom' => ['need|in:yes,no', 'no'],
        'title'  => 'when:custom=yes|string',
        'status' => 'when:custom=yes|int',
    ];
}
