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
        'order'   => 'int',
        'visibility' => 'string|ciin:world,group,pswd,owner',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function list(array $where = null, array $querys = null)
    {
        if ($search = ($querys['search'] ?? false)) {
            $this->whereTitle('like', "%{$search}%");
        }

        return $this->where($where)->get();
    }

    public function folder(string $key = null)
    {
        if ($folder = $this->belongsTo(
            DocFolder::class,
            'folder',
            'id'
        )) {
            return $key ? $folder->$key : $folder;
        }
    }

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

    public function creator(string $attr = null)
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
