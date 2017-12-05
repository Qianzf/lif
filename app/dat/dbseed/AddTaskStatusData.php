<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddTaskStatusData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('task_status')) {
            db()->truncate('task_status');
            
            db()->table('task_status')->insert([
                [
                    'key' => 'activated',
                    'val' => '待安排：刚被创建／或取消后被创建者激活',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'canceled',
                    'val' => '已取消：任务创建者取消该任务',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_dev',
                    'val' => '待开发：被任务创建者安排给一位开发者负责人',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'deving',
                    'val' => '开发中：开发者负责人确认任务并已开始开发',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_dep2test',
                    'val' => '待部署：开发人员开发完成后指派给运维',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'deploying_test',
                    'val' => '部署中：到测试环境',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_1st_test',
                    'val' => '待测试：成功部署到基本测试环境',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'waitting_fix_test',
                    'val' => '待解决：部署到基本测试环境出现问题',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'fixing_test',
                    'val' => '解决中：解决在基本测试环境部署不通过的问题',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'testing_1st',
                    'val' => '测试中：部署到测试环境成功且测试人员已在测试',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'test_back2dev',
                    'val' => '已返工：基本测试环境测试不通过',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'fixing_testback',
                    'val' => '解决中：解决在基本测试环境测试不通过的问题',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_update2test',
                    'val' => '待部署：开发人员申请重新部署到测试环境',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'waitting_dep2stage',
                    'val' => '待部署：基本测试环境测试通过，申请提测到预发布环境',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'deploying_stage',
                    'val' => '部署中：到预发布环境',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_2nd_test',
                    'val' => '待测试：成功部署到预发布环境',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'testing_2nd',
                    'val' => '测试中：部署到预发布环境成功且测试人员已在测试',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_fix_stage',
                    'val' => '待解决：部署到预发布环境出现问题',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'fixing_stage',
                    'val' => '解决中：解决在预发布环境部署不通过的问题',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'stage_back2dev',
                    'val' => '已返工：预发布环境测试不通过',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'fixing_stageback',
                    'val' => '解决中：解决在预发布环境测试不通过的问题',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_update2stage',
                    'val' => '待部署：开发人员申请重新部署到预发布环境',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'waitting_dep2prod',
                    'val' => '待上线：预发布环境测试通过，测试人员申请上线',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'deploying_prod',
                    'val' => '部署中：到正式环境',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_fix_prod',
                    'val' => '待解决：部署到正式环境出现问题',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'fixing_prod',
                    'val' => '解决中：解决部署到正式环境不通过的问题',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'waitting_update2prod',
                    'val' => '待部署：开发人员申请重新部署到正式环境',
                    'assignable' => 'yes',
                ],
                [
                    'key' => 'online',
                    'val' => '已上线：该任务已成功部署到正式环境',
                    'assignable' => 'no',
                ],
                [
                    'key' => 'finished',
                    'val' => '已验收：任务创建者已验收该任务，任务生命周期结束',
                    'assignable' => 'no',
                ],
                // [
                //     'key' => 'unacceptable',
                //     'val' => '已返工：任务创建者验收不通过',
                //     'assignable' => 'no',
                // ],
            ]);
        }
    }

    public function revert()
    {
        if (schema()->hasTable('task_status')) {
            db()->truncate('task_status');
        }
    }
}
