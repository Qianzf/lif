<?php

namespace Lif\Ctl;

class API extends Ctl
{
    public function user(\Lif\Mdl\User $user)
    {
        response([
            'id' => $user->id()
        ]);
    }
}
