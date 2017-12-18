<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddUserData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('user')) {
            dbraw('add_user_data');
        }
    }

    public function revert()
    {
        if (schema()->hasTable('user')) {
            dbraw('add_user_data.rollback');
        }
    }
}
