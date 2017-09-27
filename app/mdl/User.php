<?php

namespace Lif\Mdl;

class User extends Mdl
{
    protected $table = 'user';

    protected $unreadable = [
        'passwd',
    ];
}
