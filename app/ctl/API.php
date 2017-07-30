<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user()
    {
        response([
            'list' => [
                [
                    'name'  => 'lif',
                    'email' => 'lif@cjli.info',
                ],
                [
                    'name'  => 'me',
                    'email' => 'me@cjli.info',
                ],
            ]
        ]);
    }
}
