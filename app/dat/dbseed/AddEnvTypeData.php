<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddEnvTypeData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('env_type')) {
            db()->truncate('env_type');
            
            db()->table('env_type')->insert([
                [
                    'key' => 'test',
                    'desc' => 'Basic testing environment, should equals with production',
                ],
                [
                    'key' => 'emrg',
                    'desc' => 'Testing environment for emergency only',
                ],
                [
                    'key' => 'stage',
                    'desc' => 'Testing environmnet use production data copy',
                ],
                [
                    'key' => 'rc',
                    'desc' => 'Integration testing environmnet use production data copy',
                ],
                [
                    'key' => 'prod',
                    'desc' => 'Production environment',
                ],
            ]);
        }
    }

    public function revert()
    {
        if (schema()->hasTable('env_type')) {
            db()->truncate('env_type');
        }
    }
}
