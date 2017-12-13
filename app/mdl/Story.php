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
        'title'    => 'need|string',
        'creator'  => 'int|min:1',
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

    public function canBeEditedBy(int $user)
    {
        return (
            ($this->creator == $user)
        );
    }

    public function canBeDispatchedBy(int $user)
    {
        if ($user) {
            return (
                ($this->creator == $user)
            );
        }

        excp('Missing user id.');
    }

    public function creator(string $key = null)
    {
        if ($creator = $this->belongsTo(
            User::class,
            'creator',
            'id'
        )) {
            return $key ? $creator->$key : $creator;
        }
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
                'trending.id' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

    public function addTrending(string $action, int $user)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'user'   => $user,
            'action' => $action,
            'ref_type' => 'story',
            'ref_id'   => $this->id,
        ]);
    }
}
