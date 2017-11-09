<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function list(array $params)
    {
        $role = (share('user.role') == 'ADMIN')
        ? -1 : 'ADMIN';

        legal_or($params, [
            'user_id'   => ['int|min:1', null],
            'take_from' => ['int|min:0', 0],
            'take_cnt'  => ['int|min:0', 20],
        ]);

        if (is_null($params['user_id'])) {
            return $this
            ->leftJoin('user', 'user.id', 'trending.uid')
            ->sort([
                'trending.at' => 'desc',
            ])
            ->where('user.role', '!=', $role)
            ->limit(
                $params['take_from'],
                $params['take_cnt']
            )
            ->get();
        }

        $user = model(User::class, $params['user_id']);

        if (! $user->items()) {
            client_error('USER_NOT_FOUND', 404);
        }
        if (($user->role == 'ADMIN') && (share('user.role') != 'ADMIN')) {
            return [];
        }

        return $user->trendings($params['take_from'], $params['take_cnt']);
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
