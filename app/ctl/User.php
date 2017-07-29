<?php

namespace Lif\Ctl;

use Lif\Core\Ctl;

class User extends Ctl
{
    public function delete()
    {
        response([
            'status' => 'success'
        ]);
    }

    public function get()
    {
        response([
            'list' => [
                [
                    'name'  => 'cjli',
                    'email' => 'cjli@cjli.info',
                ],
                [
                    'name'  => 'ckloy',
                    'email' => 'ckloy@cjli.info',
                ],
            ]
        ]);
    }

    public function create()
    {
        response([
            'notes' => 'You are creating an user.',
        ]);
    }
}
