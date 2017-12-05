<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddEnvironmentData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('environment')) {
            dbraw('add_environment_data');
        }
    }

    public function revert()
    {
        if (schema()->hasTable('environment')) {
            dbraw('add_environment_data.rollback');
        }
    }
}
