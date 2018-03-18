<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class HttpapiEnv extends ModelBase
{
    protected $table = 'httpapi_env';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];
}
