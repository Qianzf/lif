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

    public function addTrending(
        string $action,
        int $user,
        int $target = null,
        string $notes = null
    )
    {
        return db()->table('trending')->insert([
            'at'        => date('Y-m-d H:i:s'),
            'user'      => $user,
            'action'    => $action,
            'ref_type'  => 'doc',
            'ref_id'    => $this->id,
            'target'    => $target,
            'notes'     => $notes,
        ]);
    }

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
