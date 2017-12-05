<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddEnvStatusData extends Dit
{
    public function commit()
    {
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

    public function revert()
    {
        if (schema()->hasTable('env_status')) {
            db()->truncate('env_status');
        }
    }
}
