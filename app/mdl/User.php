<?php

namespace Lif\Mdl;

class User extends Mdl
{
    protected $table = 'user';

    protected $unreadable = [
        'passwd',
    ];

    public function trendings()
    {
        return $this->hasMany(
            Trending::class,
            'id',
            'uid'
        );
    }
}
