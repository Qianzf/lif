<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Doc extends ModelBase
{
    protected $table  = 'doc';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'title'   => 'need|string',
        'content' => 'string',
        'creator' => 'int|min:1',
        'folder'  => 'int|min:0',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function ofUser(int $user)
    {

    }

    public function creator(string $attr)
    {
        if ($creator = $this->belongsTo(
            User::class,
            'creator',
            'id'
        )) {
            return $attr ? $creator->$attr : $creator;
        }
    }
}
