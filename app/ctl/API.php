<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user(\Lif\Mdl\User $user)
    {
        ee(db()
            ->table('checkcode c')
            ->leftJoin('jh_member m', 'c.id', '=', 'm.uid')
            // ->select('1+1')
            ->select('m.uid', 'c.id')
            // ->where([
            //     ['aid', [1, 3, 5]],
            //     ['bid', '2']
            // ])
            // ->where('cid', 4)
            // ->whereFromOrWhere(4)
            // ->where(function ($table) {
            //     $table
            //     ->where('aid', 6)
            //     ->or('cid', 7);
            // })
            // ->or('cid', 8)
            // ->or(function ($table) {
            //     $table
            //     ->whereAidBidCid(1)
            //     ->orBidCid(110);
            // })
            // ->whereAid(4)
            // ->whereAidBidCid(20, 2, 3)
            // ->orAidBidCid(4)
            ->or(function ($table) {
                // $table
                // ->where('cid','9')
                // ->or('bid','15');
            })
            ->limit(2)
            // ->sort('aid desc', 'bid asc', 'cid desc')
            // ->group('aid', 'bid', 'cid')
            ->get()
            // ->__sql()
        );

        response([
            'id' => $user->id
        ]);
    }
}
