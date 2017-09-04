<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user(\Lif\Mdl\User $user)
    {
        ee(db()
            ->table('lif as a, lif as b, lif as c')
            // ->select('1+1')
            ->select('a.id as aid', 'b.id as bid', 'c.id as cid')
            // ->where([
            //     ['aid', 'in', [1, 3, 5]],
            //     ['bid', '2']
            // ])
            // ->where('cid', 4)
            // ->where(function ($table) {
            //     $table
            //     ->where('aid = 6')
            //     ->or('cid', 7);
            // })
            ->or('cid', 8)
            ->or(function ($table) {
                $table
                ->where('cid = 9')
                ->or('bid = 10');
            })
            ->limit(2)
            ->sort('aid desc', 'bid asc', 'cid desc')
            ->group('aid', 'bid', 'cid')
            // ->get()
            ->sql()
        );

        response([
            'id' => $user->id
        ]);
    }
}
