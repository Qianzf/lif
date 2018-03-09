<?php

namespace Lif\Mdl;

class Bug extends Mdl
{
    use \Lif\Traits\TasksOriginable;
    
    protected $table = 'bug';

    protected $rules = [
        'creator' => 'int|min:1',
        'product' => 'int|min:0',
        'title'   => 'need|string',
        'how'     => 'need|string',
        'what'    => 'need|string',
        'errmsg'  => 'string',
        'errcode' => 'string',
        'os'      => 'need|string',
        'os_ver'  => 'need|string',
        'platform'     => 'need|string',
        'platform_ver' => 'need|string',
        'recurable'    => 'need|ciin:yes,no',
        'extra'        => 'string',
        'contact'      => 'string',
        'priority'     => 'int|min:0',
    ];

    public function getTaskOriginName() : string
    {
        return 'bug';
    }

    public function getAllUsers()
    {
        return db()
        ->table('user')
        ->select('id', 'name')
        ->whereStatus(1)
        ->get();
    }

    public function creator(string $key = null)
    {
        if ($bug = $this->belongsTo(
            User::class,
            'creator',
            'id'
        )) {
            return $key ? $bug->$key : $bug;
        }
    }

    public function canEdit(int $user)
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
                'id' => $order,
            ];
        }

        return $this->hasMany($relationship);
    }

    public function list(
        $selects = null,
        array $where = null,
        bool $model = true,
        array $querys = []
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
            'create_at' => ($querys['sort'] ?? 'desc'),
        ])
        ->limit(
            ($querys['from'] ?? 0),
            ($querys['take'] ?? 16)
        )
        ->all($model);
    }

    public function addTrending(string $action, int $user)
    {
        db()->table('trending')->insert([
            'at'     => date('Y-m-d H:i:s'),
            'user'   => $user,
            'action' => $action,
            'ref_type' => 'bug',
            'ref_id'   => $this->id,
        ]);
    }
}
