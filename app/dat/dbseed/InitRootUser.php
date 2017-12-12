<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class InitRootUser extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('user')) {
            db()
            ->table('user')
            ->insert([
                'name'    => 'root',
                'account' => 'root',
                'email'   => 'root@ldtdf.localhost',
                'passwd'  => '$2y$10$O77zcVwW8NC1Kcs.qg00Ye.ORy0mHwczCSIHarkBeUr9pBpMKLqRK',
                'role'    => 'admin',
                'status'  => 1,
            ]);
        }
    }

    public function revert()
    {
        if (schema()->hasTable('user')) {
            db()
            ->table('user')
            ->whereAccount('root')
            ->delete();
        }
    }
}
