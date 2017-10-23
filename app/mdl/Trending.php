<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function list(array $params)
    {
        legal_or($params, [
            'user_id'   => ['int|min:1', null],
            'take_from' => ['int|min:0', 0],
            'take_cnt'  => ['int|min:0', 20],
        ]);

        if (is_null($params['user_id'])) {
            return $this
            ->sort([
                'at' => 'desc',
            ])
            ->limit(
                $params['take_from'],
                $params['take_cnt']
            )
            ->get();
        }

        $user = model(User::class, $uid);

        if (! $user->items()) {
            excp('USER_NOT_FOUND');
        }

        return $user->hasMany([
            'model' => Trending::class,
            'lk'    => 'id',
            'fk'    => 'uid',
            'take_from' => $taskFrom,
            'take_cnt'   => $takeTo,
            'sort'  => [
                'at' => 'desc',
            ],
        ]);
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'uid',
            'id'
        );
    }
}
