<?php

namespace Lif\Traits;

use Lif\Core\Storage\SQL\Builder;

trait TaskStatus
{
    public function getAssignableUsersWhenWaittingUpdate2test(Builder $query)
    {
        return $query
        ->whereRole([
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenTestBack2dev(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaitting1stTest(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'ops',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingDep2test(Builder $query)
    {
        return $query
        ->whereRole('test')
        ->get();
    }

    public function getAssignableUsersWhenWaittingDev(Builder $query)
    {
        return $query
        ->whereRole('ops')
        ->get();
    }

    public function getAssignableUsersWhenCreated(Builder $query)
    {
        return $query
        ->whereRole('dev')
        ->get();
    }

    public function getAssignActionsWhenWaittingUpdate2test()
    {
        return [
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignActionsWhenTestBack2dev()
    {
        return [
            'WAITTING_UPDATE2TEST',
        ];
    }

    public function getAssignActionsWhenWaitting1stTest()
    {
        return [
            'TEST_BACK2DEV',
            'WAITTING_DEP2STAGE',
        ];
    }

    public function getAssignActionsWhenWaittingDep2test()
    {
        return [
            'WAITTING_1ST_TEST',
        ];
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

