<?php

namespace Lif\Mdl;

class Project extends Mdl
{
    protected $table = 'project';
    protected $rules = [
        'name'  => 'need|string',
        'type'  => ['need|in:web,app', 'web'],
        'url'   => 'need|string',
        'vcs'   => ['need|in:git', 'git'],
        'desc'  => 'string',
        'token' => ['string', null],
        'script_type' => ['need|in:local,remote', null],
        'script_path' => ['string', null],
    ];
}
