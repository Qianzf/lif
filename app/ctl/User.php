<?php

namespace Lif\Ctl;

use Lif\Core\Application as App;

class User extends App
{
    public function get()
    {
        $this->jsonResponse(200, 'success', [
            'name' => 'cjli',
        ]);
    }

    public function create()
    {
        $this->jsonResponse(200, 'success', [
            'notes' => 'You are creating an user.',
        ]);
    }
}
