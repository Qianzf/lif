<?php

namespace Lif\Mdl;

class Project extends Mdl
{
    protected $table = 'project';
    protected $rules = [
        'name'  => 'need|string',
        'type'  => ['need|ciin:web,app', 'web'],
        'url'   => 'need|string',
        'vcs'   => ['need|ciin:git', 'git'],
        'desc'  => 'string',
        'token' => ['string', null],
        'config_api'  => 'string',
        'script_type' => 'ciin:inner,outer,nil',
        'script_path' => ['string', null],
    ];

    public function deployable()
    {
        if ($this->alive()) {
            return (strtolower($this->type) == 'web');
        }

        return false;
    }

    public function environments(
        array $lwhere = [],
        array $fwhere = [],
        int $take = 10,
        int $from = 0
    )
    {
        return $this->hasMany([
            'model' => Environment::class,
            'lk' => 'id',
            'fk' => 'project',
            'from' => $from,
            'take' => $take,
            'lwhere' => $lwhere,
            'fwhere' => $fwhere,
        ]);
    }
}
