<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Upload extends ModelBase
{
    protected $table = 'upload';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'filekey'  => 'need|string',
        'filename' => 'string',
    ];
    
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];
}
