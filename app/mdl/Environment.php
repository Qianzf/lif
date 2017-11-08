<?php

namespace Lif\Mdl;

class Environment extends Mdl
{
    protected $table = 'environment';
    protected $rules = [
        'name' => 'need|string',
        'host' => 'need|host',
        'type' => ['need|in:test,stage,prod', 'test'],
        'server' => 'need|int|min:1',
    ];
}
