<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user()
    {
        // dd(db()->raw('show tables', []));
        // dd(db()->table('checkcode')->truncate());

        dd(db()->trans(function ($table) {
            $table->table('checkcode')->insert([
                // 'id' => time(),
                'value' => 'cjli@cjli.info',
            ]);
            // $table->table('checkcode')->whereId(1001)->delete();
            // $table->table('checkcode')->truncate();
        }));

        ee(db('local_sqlite')
            ->table('lif')
            // ->leftJoin('lif b', 'a.id', '=', 'b.id')
            // ->where('val', 'like', '1%')
            // ->get()
            // ->get(false, 2)
            ->insert([
                ['id' => 1001, 'val' => 'cjli@cjli.info',],
                // ['id' => 111111, 'val' => 1112223333,],
            ])
            // ->update([
            //     'val' => 44,
            // ])
            // ->__sql()
        );

        // ->update([
        //     'val' => 'test2',
        // ]));

        $xml = '1.xml';

        ee(json_encode(simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA)));

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
