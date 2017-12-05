<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddEventData extends Dit
{
    public function commit()
    {
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

    public function revert()
    {
        if (schema()->hasTable('event')) {
            db()->truncate('event');
        }
    }
}
