<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function list(int $uid = null)
    {
        if (is_null($uid)) {
            return $this
            ->sort([
                'at' => 'desc',
            ])
            ->limit(20)
            ->get();
        } elseif (1 > $uid) {
            excp('ILLEGAL_USER_ID');
        }

        $user = model(User::class, $uid);

        if (! $user->items()) {
            excp('USER_NOT_FOUND');
        }

        return $user->hasMany([
            'model' => Trending::class,
            'lk'    => 'id',
            'fk'    => 'uid',
            'sort' => [
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
