<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user(\Lif\Mdl\User $user)
    {
        dd(db()
            ->table('lif as a', [
                'b' => 'lif',
                'c' => 'lif',
            ], 'lif as d')
            // ->select('1+1')
            ->select('a.id as aid, a.id as cid', [
                'bid' => 'b.id'
            ])
            // ->where('id', 1)
            ->limit(2, 0)
            ->sort('cid, bid')
            ->group(['aid', 'bid'], 'cid')
            // ->get()
            ->sql()
        );

        response([
            'id' => $user->id
        ]);
    }
}
