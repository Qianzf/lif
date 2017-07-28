<?php

namespace Lif\Ctl;

use Lif\Core\Controller;

class User extends Controller
{
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
