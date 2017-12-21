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

    public function list(array $where = null, array $querys = null)
    {
        if ($search = ($querys['search'] ?? false)) {
            $this->whereTitle('like', "%{$search}%");
        }

        return $this->where($where)->get();
    }

    public function parent(string $key = null)
    {
        if ($parent = $this->find($this->parent)) {
            return $key ? $parent->$key : $parent;
        }
    }

    public function getTreeSelectFormattedList(int $pid = null) {
        $list = $this
        ->select('title as name','id')
        ->whereParent($pid ?? 0)
        ->all(false);

        array_walk($list, function (&$item) {
            $item['open']      = false;
            $item['isParent']  = true;
        });

        return $list;
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
            'ref_type'  => 'doc_folder',
            'ref_id'    => $this->id,
            'target'    => $target,
            'notes'     => $notes,
        ]);
    }

    public function docs(bool $model = true)
    {
        if ($this->alive()) {
            return $this
            ->hasMany([
                'model' => Doc::class,
                'lk' => 'id',
                'fk' => 'folder',
                'selects' => ['id', 'title'],
                'sort' => [
                    'order' => 'asc',
                ],
                'tomodel' => $model,
            ]);
        }
    }

    public function children($selects = '*', bool $model = true)
    {
        if ($this->alive()) {
            return $this
            ->select($selects)
            ->whereParent($this->id)
            ->sort([
                'order',
                'id',
            ])
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
        return $this
        ->select('title as name','id')
        ->whereId('!=', $this->id)
        ->get();
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
