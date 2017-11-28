<?php

namespace Lif\Mdl;

class User extends Mdl
{
    protected $table = 'user';

    protected $unreadable = [
        'passwd',
    ];

    public function getNonAdmin()
    {
        return $this->whereStatus(1)->whereRole('!=', 'admin')->get();
    }

    public function list(
        $selects = null,
        array $where = null,
        bool $model = true
    )
    {
        $selects = $selects ?? '*';
        $query   = $this
        ->select($selects)
        ->whereStatus(1);

        if ($where) {
            $query = $query->where($where);
        }

        return $query->limit(0, 20)->all($model);
    }

    public function login(string $account)
    {
        return $this
        ->whereStatus(1)
        ->where(function ($user) use ($account) {
            $user
            ->whereAccount($account)
            ->orEmail($account);
        })
        ->first();
    }

    public function inGroup($gid) : bool
    {
        if (! $this->isAlive()
            || !($user = $this->getPK())
        ) {
            excp('Can not determine if user in group when user is not alive.');
        }

        return !empty_safe(
            db()
            ->table('user_group_map')
            ->whereUserGroup($user, $gid)
            ->get()
        );
    }

    public function trendings(int $start = 0, int $offset = 16)
    {
        return $this->hasMany([
            'model' => Trending::class,
            'lk'    => 'id',
            'fk'    => 'user',
            'from'  => $start,
            'take'  => $offset,
            'sort'  => [
                'at' => 'desc',
            ],
        ]);
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
                        $_attr[] = [
                            'col' => db()->native(
                                'lower('.escape_fields($key).')'
                            ),
                            'val' => strtolower($item),
                        ];
                    });

                    $query = $query->or($_attr);

                    unset($_attr);
                }
            })->count() > 0
        );
    }
}
