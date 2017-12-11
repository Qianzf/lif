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
                'email'   => 'root@localhost',
                'passwd'  => '$2y$10$8s.A665uEoKew7Zc2zNYAuI3Qk3hiQPV7/Ix0XjrtXTSHFp0xHd3e',
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
