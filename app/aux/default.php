<?php

// -------------------------------------
//     User defined Helper Functions
// -------------------------------------

if (! fe('init_dit_table')) {
    function init_dit_table() {
        schema()
        ->setAutocommit(false)
        ->createIfNotExists('__dit__', function ($table) {
            $table->pk('id');
            $table
            ->string('name')
            ->charset('utf8')
            ->collate('utf8_unicode_ci')
            ->unique();
            $table->tinyint('version')->default(1);
            $table
            ->timestamp('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->charset('utf8')
            ->collate('utf8_unicode_ci');
        })
        ->commit();
    }
}
if (! fe('init_job_table')) {
    function init_job_table() {
        schema()
        ->setAutocommit(false)
        ->createIfNotExists('__job__', function ($table) {
            $table->pk('id');
            $table->string('queue');
            $table->text('detail');
            
            $table
            ->tinyint('try')
            ->default(0)
            ->comment('How many tried times to be consider as failed');
            
            $table
            ->tinyint('tried')
            ->default(0)
            ->comment('Tried times of this job in current try loop');
            
            $table
            ->tinyint('retried')
            ->unsigned()
            ->comment('Failed times of this job')
            ->default(0);
            
            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);

            $table
            ->tinyint('timeout')
            ->unsigned()
            ->comment('The max execution time for this job');

            $table
            ->tinyint('restart')
            ->default(0)
            ->comment('Should this job need to be restarted');
            
            $table
            ->tinyint('lock')
            ->default(0)
            ->comment('Job running or not');

            $table->comment('Queue job table');
        })
        ->commit();
    }
}
if (! fe('prepare_user_role_data')) {
    function prepare_user_role_data() {
        if (schema()->hasTable('user_role')) {
            db()->truncate('user_role');

            db()->table('user_role')->insert([
                ['key' => 'admin', 'desc' => 'System Admin',],
                ['key' => 'pm', 'desc'   => 'Software product manager',],
                ['key' => 'dev', 'desc'   => 'IT System operator',],
                ['key' => 'ops', 'desc'   => 'System Admin',],
                ['key' => 'test', 'desc'  => 'Software Testing Engineer',],
                ['key' => 'ui', 'desc'  => 'User Interface designer',],
            ]);
        }
    }
}
if (! fe('prepare_task_status_data')) {
    function prepare_task_status_data() {
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
}
if (! fe('prepare_event_data')) {
    function prepare_event_data() {
        if (schema()->hasTable('event')) {
            db()->truncate('event');
            db()->table('event')->insert([
                [
                    'key' => 'login_sys',
                    'desc' => 'User Loggedin system',
                ],
                [
                    'key' => 'create_task',
                    'desc' => 'A task has been created by an user',
                ],
                [
                    'key' => 'assign_task',
                    'desc' => 'A task has been assigned to an user',
                ],
                [
                    'key' => 'report_bug',
                    'desc' => 'A bug task has been created by an user',
                ],
                [
                    'key' => 'assign_bug',
                    'desc' => 'A bug has been assigned to an user',
                ],
                [
                    'key' => 'comment_task',
                    'desc' => 'A user has commented a task',
                ],
                [
                    'key' => 'comment_bug',
                    'desc' => 'A user has commented a bug',
                ],
                [
                    'key' => 'update_task',
                    'desc' => 'A user has updated a task',
                ],
                [
                    'key' => 'update_bug',
                    'desc' => 'A user has updated a bug',
                ],
                [
                    'key' => 'update_task_comment',
                    'desc' => 'A user has updated a comment of a task',
                ],
                [
                    'key' => 'update_bug_comment',
                    'desc' => 'A user has updated a comment of a bug',
                ],
            ]);
        }
    }
}
if (! fe('prepare_env_data')) {
    function prepare_env_data() {
        if (schema()->hasTable('env_type')) {
            db()->truncate('env_type');
            db()->table('env_type')->insert([
                [
                    'key' => 'test',
                    'desc' => 'Basic testing environment',
                ],
                [
                    'key' => 'emrg',
                    'desc' => 'Same as testing environment, for emergency only',
                ],
                [
                    'key' => 'stage',
                    'desc' => 'Same as testing environmnet, use production data copy',
                ],
                [
                    'key' => 'prod',
                    'desc' => 'Production environment',
                ],
            ]);
        }
        if (schema()->hasTable('env_status')) {
            db()->truncate('env_status');
            db()->table('env_status')->insert([
                [
                    'key' => 'running',
                    'desc' => 'Environment is running regularly',
                ],
                [
                    'key' => 'locked',
                    'desc' => 'Environment is locked for one task',
                ],
                [
                    'key' => 'stopped',
                    'desc' => 'Environment is stopped and not serving anymore',
                ],
            ]);
        }
    }
}