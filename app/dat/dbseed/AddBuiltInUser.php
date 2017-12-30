<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddBuiltInUser extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('user')) {
            db()
            ->table('user')
            ->insert([
                [
                    'name'    => 'root',
                    'account' => 'root',
                    'email'   => 'root@ldtdf.local',
                    'passwd'  => '$2y$10$u/jGvqxLQ0XqqPUp3p2SAe5xEtntL2qMrYJWM7IKqCVpvJA.btHVW',
                    'role'    => 'admin',
                    'status'  => 1,
                ],
                [
                    'name'    => 'robot',
                    'account' => 'robot',
                    'email'   => 'robot@ldtdf.local',
                    'passwd'  => '$2y$10$h6zAbf5O.7qKBtJsTidFW.uu2S1/rJQamAwVA/T4ADnIQ9gf31pxG',
                    'role'    => 'ops',
                    'status'  => 1,
                ],
            ]);
        }
    }

    public function revert()
    {
        if (schema()->hasTable('user')) {
            db()
            ->table('user')
            ->whereAccount(['root', 'robot'])
            ->delete();
        }
    }
}
