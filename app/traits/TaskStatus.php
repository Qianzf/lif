<?php

namespace Lif\Traits;

use Lif\Core\Storage\SQL\Builder;

trait TaskStatus
{
    public function getAllStatus()
    {
        $status = db()
        ->table('task_status')
        ->select(function () {
            return 'UPPER(`key`) AS `status`';
        })
        ->where('assignable', 'yes')
        ->get();

        return array_column($status, 'status');
    }

    public function getAssignableUsersWhenEnvConfirmed($query)
    {
        return $query->whereRole('test');
    }

    public function getUnoperatableStatus()
    {
        return [
            'waitting_regression',
            'regression_testing',
            'stablerc_back2self',
            'stablerc_back2other',
            'waitting_newfix_stablerc',
            'waitting_dep2prod',
            'deploying_prod',
            'waitting_fix_prod',
            'fixing_prod',
            'canceled',
            'finished',
            'online',
            'finished',
        ];
    }

    public function getActionsOfRoleDev()
    {
        return [
            'WAITTING_DEV',
            'WAITTING_FIX_TEST',
            'WAITTING_FIX_STAGE',
            'WAITTING_FIX_STABLERC',
            'WAITTING_FIX_PROD',
            'TEST_BACK2DEV',
            'STAGE_BACK2DEV',
        ];
    }

    public function getActionsOfRoleOps()
    {
        return [
            'WAITTING_DEP2TEST',
            'WAITTING_DEP2STAGE',
            'WAITTING_DEP2STABLERC',
            'WAITTING_DEP2PROD',
        ];
    }

    public function getActionsOfRoleTest()
    {
        return [
            'WAITTING_1ST_TEST',
            'WAITTING_2ND_TEST',
            'WAITTING_REGRESSION',
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

    public function getAssignableStatusesWhenFixingStablerc()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_DEP2STABLERC',
            'WAITTING_FIX_STABLERC',
        ];
    }

    public function getAssignableStatusesWhenFixingStageback()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignableStatusesWhenFixingTestback()
    {
        return [
            'WAITTING_DEP2TEST',
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignableStatusesWhenFixingProd()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_FIX_PROD',
            'WAITTING_DEP2PROD',
        ];
    }

    public function getAssignableStatusesWhenFixingStage()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_2ND_TEST',
            'WAITTING_FIX_STAGE',
        ];
    }

    public function getAssignableStatusesWhenFixingTest()
    {
        return [
            'WAITTING_DEP2TEST',
            'WAITTING_1ST_TEST',
            'WAITTING_DEV',
        ];
    }

    public function getAssignableStatusesWhenDeployingStage()
    {
        return [
            'WAITTING_2ND_TEST',
            'WAITTING_FIX_STAGE',
        ];
    }

    public function getAssignableStatusesWhenDeployingStablerc()
    {
        return [
            'WAITTING_REGRESSION',
            'WAITTING_FIX_STABLERC',
        ];
    }

    public function getAssignableStatusesWhenDeployingProd()
    {
        return [
            'ONLINE',
            'WAITTING_FIX_PROD',
        ];
    }

    public function getAssignableStatusesWhenFinished()
    {
        return [
            'UNACCEPTABLE',
        ];
    }

    public function getAssignableStatusesWhenCanceled()
    {
    }
    
    public function getAssignableStatusesWhenOnline()
    {
    }

    public function getAssignableStatusesWhenWaittingFixStablerc()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_DEP2STABLERC',
            'WAITTING_FIX_STABLERC',
        ];
    }
    
    public function getAssignableStatusesWhenWaittingFixProd()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_FIX_PROD',
            'WAITTING_DEP2PROD',
        ];
    }

    public function getAssignableStatusesWhenWaittingFixStage()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_2ND_TEST',
            'WAITTING_FIX_STAGE',
        ];
    }

    public function getAssignableStatusesWhenWaittingFixTest()
    {
        return [
            'WAITTING_DEP2TEST',
            'WAITTING_DEV',
        ];
    }

    public function getAssignableStatusesWhenTesting2nd()
    {
        return [
            'WAITTING_DEP2STABLERC',
            'WAITTING_2ND_TEST',
            'STAGE_BACK2DEV',
        ];
    }

    public function getAssignableStatusesWhenEnvConfirmed()
    {
        return [
            'WAITTING_1ST_TEST',
        ];
    }
    
    public function getAssignableStatusesWhenWaittingConfirmEnv()
    {
        return [
            'WAITTING_1ST_TEST',
            'WAITTING_DEP2TEST',
        ];
    }

    public function getAssignableStatusesWhenTesting1st()
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

    public function getAssignableStatusesWhenDeployingTest()
    {
        return [
            'WAITTING_1ST_TEST',
            'WAITTING_FIX_TEST',
        ];
    }

    public function getAssignableStatusesWhenStageBack2dev()
    {
        return [
            'WAITTING_DEP2STAGE',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignableStatusesWhenWaitting2ndTest()
    {
        return [
            'WAITTING_DEP2STABLERC',
            'STAGE_BACK2DEV',
            'WAITTING_2ND_TEST',
        ];
    }

    public function getAssignableStatusesWhenWaittingDep2prod()
    {
        return [
            'ONLINE',
            'WAITTING_FIX_PROD',
        ];
    }

    public function getAssignableStatusesWhenWaittingDep2stablerc()
    {
        return [
            'WAITTING_REGRESSION',
            'WAITTING_FIX_STABLERC',
        ];
    }

    public function getAssignableStatusesWhenWaittingDep2stage()
    {
        return [
            'WAITTING_2ND_TEST',
            'WAITTING_FIX_STAGE',
        ];
    }

    public function getAssignableStatusesWhenTestBack2dev()
    {
        return [
            'WAITTING_DEP2TEST',
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignableStatusesWhenWaitting1stTest()
    {
        return [
            'TEST_BACK2DEV',
            'WAITTING_DEP2STAGE',
        ];
    }

    public function getAssignableStatusesWhenWaittingDep2test()
    {
        return [
            'WAITTING_1ST_TEST',
            'WAITTING_FIX_TEST',
        ];
    }

    public function getAssignableStatusesWhenDeving()
    {
        return [
            'WAITTING_DEP2TEST',
            'WAITTING_DEV',
        ];
    }

    public function getAssignableStatusesWhenWaittingDev()
    {
        return $this->isForWeb()
        ? [
            'WAITTING_DEP2TEST',
            'WAITTING_DEV',
        ] : [
            'WAITTING_1ST_TEST',
        ];
    }

    public function getAssignableStatusesWhenActivated()
    {
        return [
            'WAITTING_DEV',
        ];
    }

    public function getStatusConfirmed() : string
    {
        $before = strtolower($this->status);
        $map = [
            'waitting_dev'          => 'DEVING',
            'waitting_dep2test'     => 'DEPLOYING_TEST',
            'waitting_confirm_env'  => 'ENV_CONFIRMED',
            'waitting_1st_test'     => 'TESTING_1ST',
            'waitting_fix_test'     => 'FIXING_TEST',
            'test_back2dev'         => 'FIXING_TESTBACK',
            'waitting_dep2stage'    => 'DEPLOYING_STAGE',
            'waitting_fix_stage'    => 'FIXING_STAGE',
            'waitting_2nd_test'     => 'TESTING_2ND',
            'stage_back2dev'        => 'FIXING_STAGEBACK',
            'waitting_dep2stablerc' => 'DEPLOYING_STABLERC',
            'waitting_fix_stablerc' => 'FIXING_STABLERC',
            'waitting_dep2prod'     => 'DEPLOYING_PROD',
            'waitting_regression'   => 'REGRESSION_TESTING',
            'stablerc_back2self'    => 'FIXING_STABLERCBACK',
            'waitting_fix_prod'     => 'FIXING_PROD',
            'online'                => 'FINISHED',
            'deploying_prod'        => 'ONLINE',
        ];

        return $map[$before] ?? 'UNKNOWN';
    }
}
