<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class HttpapiCate extends ModelBase
{
    protected $table = 'httpapi_cate';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'name' => 'string',
        'project' => 'int|min:1',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function apis()
    {
        if (! $this->alive()) {
            return [];
        }

        return Httpapi::whereCate($this->id)->all();
    }
}

