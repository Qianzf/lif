<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class DocFolder extends ModelBase
{
    protected $table = 'doc_folder';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    
    // validation rules for fields
    protected $rules  = [
        'title'   => 'need|string',
        'desc'    => 'string',
        'creator' => 'int|min:1',
        'parent'  => ['int|min:0', 0],
        'order'   => 'int',
        'visibility' => 'string|ciin:world,group,pswd,owner',
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
            'ref_type'  => 'doc_folder',
            'ref_id'    => $this->id,
            'target'    => $target,
            'notes'     => $notes,
        ]);
    }

    public function docs(bool $model = true)
    {
        if ($this->isAlive()) {
            return $this
            ->hasMany([
                'model' => Doc::class,
                'lk' => 'id',
                'fk' => 'folder',
                'selects' => ['id', 'title'],
                'tomodel' => $model,
            ]);
        }
    }

    public function children(
        $selects = '*',
        bool $model = true
    )
    {
        if ($this->isAlive()) {
            return $this
            ->select($selects)
            ->whereParent($this->id)
            ->all($model);
        }
    }

    public function firstDoc()
    {
        return $this->belongsTo(
            Doc::class,
            'id',
            'folder'
        );
    }

    public function listOthers()
    {
        return $this->whereId('!=', $this->id)->get();
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
