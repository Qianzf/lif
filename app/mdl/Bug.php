<?php

namespace Lif\Mdl;

class Bug extends Mdl
{
    protected $table = 'bug';

    protected $rules = [
        'title' => 'need|string',
        'how' => 'need|string',
        'what' => 'need|string',
        'errmsg' => 'string',
        'errcode' => 'string',
        'os' => 'need|string',
        'os_ver' => 'need|string',
        'platform' => 'need|string',
        'platform_ver' => 'need|string',
        'recurable' => 'need|ciin:yes,no',
        'extra' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'creator',
            'id'
        );
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

    public function tasks()
    {
        $relationship = [
            'model' => Task::class,
            'lk' => 'id',
            'fk' => 'origin_id',
            'fwhere' => [
                'origin_type' => 'bug',
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
                'ref_type' => 'bug',
            ],
        ];

        if ($order = ($querys['trending'] ?? null)) {
            $relationship['sort'] = [
                'at' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

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

    public function addTrending(string $action)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'user'   => share('user.id'),
            'action' => $action,
            'ref_type' => 'bug',
            'ref_id'   => $this->id,
        ]);
    }
}
