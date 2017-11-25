<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Story extends ModelBase
{
    protected $table = 'story';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'custom'   => ['in:yes,no', 'no'],
        'url'      => 'when:custom=no|need|url',
        'role'     => 'when:custom=yes|need|string',
        'activity' => 'when:custom=yes|need|string',
        'value'    => 'when:custom=yes|need|string',
        'acceptances' => 'when:custom=yes|need|string',
        'extra'       => 'when:custom=yes|string',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];
}
