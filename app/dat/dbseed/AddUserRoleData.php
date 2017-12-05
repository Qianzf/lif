<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddUserRoleData extends Dit
{
    public function commit()
    {
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

    public function revert()
    {
        if (schema()->hasTable('user_role')) {
            db()->truncate('user_role');
        }
    }
}
