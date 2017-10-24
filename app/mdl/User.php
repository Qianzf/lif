<?php

namespace Lif\Mdl;

class User extends Mdl
{
    protected $table = 'user';

    protected $unreadable = [
        'passwd',
    ];

    public function trendings()
    {
        return $this->hasMany(
            Trending::class,
            'id',
            'uid'
        );
    }

    public function hasConflict($attrs) : bool
    {
        return (
            db()
            ->table($this->table)
            ->where(function ($query) use ($attrs) {
                $_attr = [];
                foreach ($attrs as $attr) {
                    array_walk($attr, function ($item, $key) use (&$_attr) {
                        $_attr[][
                            'lower('.escape_fields($key).')'
                        ] = strtolower($item);
                    });

                    $query = $query->or($_attr);

                    unset($_attr);
                }
            })->count() > 0
        );
    }
}
