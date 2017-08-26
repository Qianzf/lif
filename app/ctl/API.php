<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user(\Lif\Mdl\User $user)
    {
        ee(db()
            ->table('lif as a, lif as b')
            // ->select('1+1')
            ->select('a.id as aid, a.id as cid', [
                'bid' => 'b.id'
            ])
            ->where('aid', 'in', []
                // ['bid', '=', 2]
            )
            ->limit(2, 0)
            ->sort('cid desc, bid asc', [
                'aid' => 'desc',
                'bid' => 'asc',
            ])
            ->group(['aid', 'bid'], 'cid')
            // ->get()
            ->sql()
        );

        response([
            'id' => $user->id
        ]);
    }
}
