<?php

namespace Lif\Mdl;

use Lif\Core\Mdl as ModelBase;

class Story extends ModelBase
{
    protected $table = 'story';
    protected $_tbx   = null;
    protected $_fdx   = null;
    protected $pk     = 'id';
    protected $alias  = null;
    // validation rules for fields
    protected $rules  = [
        'role'     => 'need|string',
        'activity' => 'need|string',
        'value'    => 'need|string',
        'acceptances' => 'need|string',
        'extra'       => 'string',
    ];
    // protected items that cann't update
    protected $unwriteable = [
    ];
    // protected items that cann't read
    protected $unreadable  = [
    ];

    public function list(
        $selects = null,
        array $where = null,
        bool $model = true
    )
    {
        $selects = $selects ?? '*';
        $query   = $this
        ->reset()
        ->select($selects);

        if ($where) {
            $query = $query->where($where);
        }

        return $query
        ->sort([
            'create_at' => 'desc',
        ])
        ->limit(0, 20)
        ->all($model);
    }

    public function canEdit()
    {
        return (
            ($this->creator == share('user.id'))
        );
    }

    public function canBeDispatchedBy(int $user = null)
    {
        if ($user = $user ?? (share('user.id') ?? null)) {
            return (
                ($this->creator == $user)
            );
        }

        excp('Missing user id.');
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'creator',
            'id'
        );
    }

    public function tasks()
    {
        $relationship = [
            'model' => Task::class,
            'lk' => 'id',
            'fk' => 'origin_id',
            'fwhere' => [
                'origin_type' => 'story',
            ],
        ];

        return $this->hasMany($relationship);
    }

    public function trendings(array $querys = [])
    {
        $relationship = [
            'model' => Trending::class,
            'lk' => 'id',
            'fk' => 'ref_id',
            'fwhere' => [
                'ref_type' => 'story',
            ],
        ];

        if ($order = ($querys['trending'] ?? null)) {
            $relationship['sort'] = [
                'at' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

    public function addTrending(string $action)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'user'   => share('user.id'),
            'action' => $action,
            'ref_type' => 'story',
            'ref_id'   => $this->id,
        ]);
    }
}
