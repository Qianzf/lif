<?php

namespace Lif\Ctl;

use Lif\Core\Controller;

class User extends Controller
{
    public function get()
    {
        $this->response([
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
        $this->response([
            'notes' => 'You are creating an user.',
        ]);
    }
}
