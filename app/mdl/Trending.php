<?php

namespace Lif\Mdl;

class Trending extends Mdl
{
    protected $table = 'trending';

    public function list($uid = null)
    {
        if (! $uid) {
            return $this->all();
        }

        return model(User::class, $uid)
        ->hasMany(Trending::class, 'id', 'uid');
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
