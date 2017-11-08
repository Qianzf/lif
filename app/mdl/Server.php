<?php

namespace Lif\Mdl;

class Server extends Mdl
{
    protected $table = 'server';
    protected $rules = [
        'name' => 'string',
        'host' => 'need|host',
        'port' => ['int|min:1', 22],
    ];
}
