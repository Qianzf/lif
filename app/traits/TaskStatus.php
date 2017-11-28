<?php

namespace Lif\Traits;

trait TaskStatus
{
    public function getAssignableUsersWhenCreated()
    {
        return db()
        ->table('user')
        ->whereRole('dev')
        ->get();
    }

    public function getAssignActionsWhenWaittingDev()
    {
        return [
            'WAITTING_DEP2TEST',
        ];
    }

    public function getAssignActionsWhenCreated()
    {
        return [
            'WAITTING_DEV',
        ];
    }
}
