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
            'WAITTING_FIX_PROD',
            'TEST_BACK2DEV',
            'STAGE_BACK2DEV',
        ];
    }

    public function getActionsOfRoleOps()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_UPDATE2STAGE',
            'WAITTING_UPDATE2PROD',
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
            'ONLINE',
            'FINISHED',
        ];
    }

    public function getAssignableUsersWhenDeployingProd(Builder $query)
    {
        return $query
        ->whereId($this->creator)
        ->orRole('dev')
        ->get();
    }

    public function getAssignableUsersWhenWaittingFixProd(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
            'dev',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingFixStage(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
            'dev',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingFixTest(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
            'dev',
        ])
        ->get();
    }

    public function getAssignableUsersWhenTesting2nd(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenEnvConfirmed(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingConfirmEnv(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
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

    public function getAssignableUsersWhenDeployingStage(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
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

    public function getAssignableUsersWhenFixingProd(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'dev',
        ])
        ->get();
    }

    public function getAssignableUsersWhenFixingStage(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenFixingStageback(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenFixingTestback(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenFixingTest(Builder $query)
    {
        return $query
        ->whereRole([
            'ops',
            'test',
        ])
        ->get();
    }

    public function getAssignableUsersWhenDeving(Builder $query)
    {
        return $query
        ->whereRole([
            'dev',
            'ops',
        ])
        ->get();
    }

    public function getAssignableUsersWhenWaittingUpdate2prod(Builder $query)
    {
        return $query
        ->whereId($this->creator)
        ->orRole([
            'dev',
            'test',
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

    public function getAssignableUsersWhenWaittingDep2prod(Builder $query)
    {
        return $query
        ->whereId($this->creator)
        ->orRole([
            'dev',
            'test',
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
        $roles = $this->isForWeb() ? ['ops','dev',] : ['dev', 'test'];

        return $query
        ->whereRole($roles)
        ->get();
    }

    public function getAssignableUsersWhenOnline(Builder $query)
    {

    }

    public function getAssignableUsersWhenFinished(Builder $query)
    {
        return $query
        ->whereRole('dev')
        ->get();
    }

    public function getAssignableUsersWhenActivated(Builder $query)
    {
        return $query
        ->whereRole('dev')
        ->get();
    }

    public function getAssignActionsWhenFixingStageback()
    {
        return [
            'WAITTING_UPDATE2STAGE',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignActionsWhenFixingTestback()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignActionsWhenFixingProd()
    {
        return [
            'WAITTING_UPDATE2STAGE',
            'WAITTING_FIX_PROD',
            'WAITTING_UPDATE2PROD',
        ];
    }

    public function getAssignActionsWhenFixingStage()
    {
        return [
            'WAITTING_UPDATE2STAGE',
            'WAITTING_2ND_TEST',
            'WAITTING_FIX_STAGE',
        ];
    }

    public function getAssignActionsWhenFixingTest()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
            'WAITTING_DEV',
        ];
    }

    public function getAssignActionsWhenDeployingStage()
    {
        return [
            'WAITTING_FIX_STAGE',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignActionsWhenDeployingProd()
    {
        return [
            'ONLINE',
            'WAITTING_FIX_PROD',
        ];
    }

    public function getAssignActionsWhenFinished()
    {
        return [
            'UNACCEPTABLE',
        ];
    }

    public function getAssignActionsWhenCanceled()
    {

    }
    
    public function getAssignActionsWhenOnline()
    {

    }

    public function getAssignActionsWhenWaittingFixProd()
    {
        return [
            'WAITTING_UPDATE2STAGE',
            'WAITTING_FIX_PROD',
            'WAITTING_UPDATE2PROD',
        ];
    }

    public function getAssignActionsWhenWaittingFixStage()
    {
        return [
            'WAITTING_UPDATE2STAGE',
            'WAITTING_2ND_TEST',
            'WAITTING_FIX_STAGE',
        ];
    }

    public function getAssignActionsWhenWaittingFixTest()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
            'WAITTING_DEV',
        ];
    }

    public function getAssignActionsWhenTesting2nd()
    {
        return [
            'WAITTING_DEP2PROD',
            'WAITTING_2ND_TEST',
            'STAGE_BACK2DEV',
        ];
    }

    public function getAssignActionsWhenEnvConfirmed()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
        ];
    }
    
    public function getAssignActionsWhenWaittingConfirmEnv()
    {
        return [
            'WAITTING_UPDATE2TEST',
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignActionsWhenTesting1st()
    {
        return $this->isForWeb()
        ? [
            'WAITTING_DEP2STAGE',
            'WAITTING_1ST_TEST',
            'TEST_BACK2DEV',
        ] : [
            'WAITTING_DEP2STAGE',
            'TEST_BACK2DEV',
        ];
    }

    public function getAssignActionsWhenDeployingTest()
    {
        return [
            'WAITTING_FIX_TEST',
            'WAITTING_1ST_TEST',
        ];
    }
    
    public function getAssignActionsWhenWaittingUpdate2prod()
    {
        return [
            'ONLINE',
            'WAITTING_FIX_PROD',
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
            'WAITTING_DEP2PROD',
            'STAGE_BACK2DEV',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignActionsWhenWaittingDep2prod()
    {
        return [
            'ONLINE',
            'WAITTING_FIX_PROD',
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
            'WAITTING_DEV',
        ];
    }

    public function getAssignActionsWhenWaittingDev()
    {
        return $this->isForWeb()
        ? [
            'WAITTING_DEP2TEST',
            'WAITTING_DEV',
        ] : [
            'WAITTING_1ST_TEST',
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
        switch (strtolower($this->status)) {
            case 'online':
                $status = 'FINISHED';
                break;
            case 'deploying_prod':
            default:
                $status = 'ONLINE';
                break;
        }

        return $status;
    }

    public function confirmWhenTest() : string
    {
        switch (strtolower($this->status)) {
            case 'waitting_2nd_test':
                $status = 'TESTING_2ND';
                break;
            case 'waitting_1st_test':
            default:
                $status = 'TESTING_1ST';
                break;
        }

        return $status;
    }

    public function confirmWhenOps() : string
    {
        switch (strtolower($this->status)) {
            case 'waitting_update2prod':
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
        switch (strtolower($this->status)) {
            case 'waitting_fix_test':
                $status = 'FIXING_TEST';
                break;
            case 'test_back2dev':
                $status = 'FIXING_TESTBACK';
                break;
            case 'waitting_fix_stage':
                $status = 'FIXING_STAGE';
                break;
            case 'stage_back2dev':
                $status = 'FIXING_STAGEBACK';
                break;
            case 'waitting_fix_prod':
                $status = 'FIXING_PROD';
                break;
            case 'waitting_confirm_env':
                $status = 'ENV_CONFIRMED';
                break;
            case 'waitting_dev':
            default:
                $status = 'DEVING';
                break;
        }

        return $status;
    }
}
