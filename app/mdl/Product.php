<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Product extends ModelBase
{
    protected $table = 'product';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'name' => 'string',
        'desc' => 'string',
        'creator' => 'int|min:1',
        'order'   => 'int',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function creator(string $key = null)
    {
        $creator = $this->belongsTo(
            User::class,
            'creator',
            'id'
        );

        if (! $creator) {
            excp(L('MISSING_PROJECT_CREATOR'));
        }

        return $key ? $creator->$key : $creator;
    }
}
