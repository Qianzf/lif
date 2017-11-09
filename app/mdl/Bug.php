<?php

namespace Lif\Mdl;

class Bug extends Mdl
{
    protected $table = 'bug';

    protected $rules = [
        'custom' => ['need|in:yes,no', 'no'],
        'title'  => 'when:custom=yes|string',
        'status' => 'when:custom=yes|int',
        'url'    => 'when:custom=no|url',
    ];
}
