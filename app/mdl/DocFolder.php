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
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

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
