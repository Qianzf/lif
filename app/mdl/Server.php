<?php

namespace Lif\Mdl;

class Server extends Mdl
{
    protected $table = 'server';
    protected $rules = [
        'name' => 'string',
        'location' => ['ciin:local,remote', 'remote'],
        'host' => 'need|host',
        'port' => ['int|min:1|max:65535', 22],
        'user' => ['string', 'root'],
        'pubk' => 'string',
        'prik' => 'string',
    ];
}
