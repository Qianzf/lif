<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class HttpapiProject extends ModelBase
{
    protected $table = 'httpapi_project';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'name' => 'string',
        'desc' => 'string',
        'creator' => 'int|min:1',
        'visibility' => 'string|ciin:world,group,pswd,owner'
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function list(array $querys)
    {
        if ($search = ($querys['search'] ?? false)) {
            $this->whereName('like', "%{$search}%");
        }

        if ($creator = ($querys['creator'] ?? false)) {
            $this->whereCreator($creator);
        }

        return $this
        ->sort([
            'create_at' => ($querys['sort'] ?? 'desc')
        ])
        ->get();
    }

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
