<?php

namespace Lif\Mdl;

class Project extends Mdl
{
    protected $table = 'project';
    protected $rules = [
        'name' => 'need|string',
        'url'  => 'need|string',
        'vcs'  => ['need|in:git', 'git'],
        'desc' => 'string',
    ];
}
