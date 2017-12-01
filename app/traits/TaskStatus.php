<?php

namespace Lif\Traits;

use Lif\Core\Storage\SQL\Builder;

trait TaskStatus
{
    public function getActionsOfRoleDev()
    {
        return [
            'WAITTING_DEV',
            'WAITTING_FIX_TEST',
            'WAITTING_FIX_STAGE',
            'TEST_BACK2DEV',
            'STAGE_BACK2DEV',
        ];
    }

    public function getActionsOfRoleOps()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_UPDATE2STAGE',
            'WAITTING_DEP2TEST',
            'WAITTING_DEP2STAGE',
            'WAITTING_DEP2PROD',
        ];
    }

    public function getActionsOfRoleTest()
    {
        return [
            'WAITTING_1ST_TEST',
            'WAITTING_2ND_TEST',
            'WAITTING_DEP2PROD',
        ];
    }

    public function getActionsOfRoleAdmin()
    {
        return [
        ];
    }

    public function getAssignableUsersWhenDeployingProd(Builder $query)
    {
        return $query
        ->whereId($this->creator)
        ->get();
    }

    public function getAssignableUsersWhenWaittingFixTest(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingOnline(Builder $query)
    {

    }

    public function getAssignableUsersWhenTesting1st(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenDeployingTest(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenDeving(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingUpdate2stage(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenStageBack2dev(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaitting2ndTest(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'ops',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingDep2stage(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingUpdate2test(Builder $query)
    {
        return $query
        ->whereRole([
            'test',
            'dev',
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
        ->whereRole([
            'test',
            'dev'
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingDev(Builder $query)
    {
        return $query
        ->whereRole('ops')
        ->get();
    }

    public function getAssignableUsersWhenActivated(Builder $query)
    {
        return $query
        ->whereRole('dev')
        ->get();
    }

    public function getAssignActionsWhenDeployingProd()
    {
        return [
            'FINISHED',
        ];
    }

    public function getAssignActionsWhenCanceled()
    {

    }
    
    public function getAssignActionsWhenWaittingOnline()
    {

    }

    public function getAssignActionsWhenWaittingFixTest()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignActionsWhenTesting1st()
    {
        return [
            'WAITTING_1ST_TEST',
            'TEST_BACK2DEV',
            'WAITTING_DEP2STAGE',
        ];
    }

    public function getAssignActionsWhenDeployingTest()
    {
        return [
            'WAITTING_FIX_TEST',
            'WAITTING_1ST_TEST',
        ];
    }
    
    public function getAssignActionsWhenWaittingUpdate2stage()
    {
        return [
            'WAITTING_FIX_STAGE',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignActionsWhenStageBack2dev()
    {
        return [
            'WAITTING_UPDATE2STAGE',
        ];
    }

    public function getAssignActionsWhenWaitting2ndTest()
    {
        return [
            'STAGE_BACK2DEV',
            'WAITTING_DEP2PROD',
        ];
    }

    public function getAssignActionsWhenWaittingDep2stage()
    {
        return [
            'WAITTING_FIX_STAGE',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignActionsWhenWaittingUpdate2test()
    {
        return [
            'WAITTING_1ST_TEST',
            'WAITTING_FIX_TEST',
        ];
    }

    public function getAssignActionsWhenTestBack2dev()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
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
            'WAITTING_FIX_TEST',
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignActionsWhenDeving()
    {
        return [
            'WAITTING_DEP2TEST',
        ];
    }

    public function getAssignActionsWhenWaittingDev()
    {
        return [
            'WAITTING_DEP2TEST',
        ];
    }

    public function getAssignActionsWhenActivated()
    {
        return [
            'WAITTING_DEV',
        ];
    }

    public function confirmWhenAdmin() : string
    {
        return '';
    }

    public function confirmWhenTest() : string
    {
        return 'TESTING_1ST';
    }

    public function confirmWhenOps() : string
    {
        switch (strtolower($this->status)) {
            case 'waitting_dep2prod':
                $status = 'DEPLOYING_PROD';
                break;
            case 'waitting_update2stage':
            case 'waitting_dep2stage':
                $status = 'DEPLOYING_STAGE';
                break;
            case 'waitting_update2test':
            case 'waitting_dep2test':
            default:
                $status = 'DEPLOYING_TEST';
                break;
        }

        return $status;
    }

    public function confirmWhenDev() : string
    {
        return 'DEVING';
    }
}
