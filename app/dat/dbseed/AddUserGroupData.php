<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddUserGroupData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('user_group')) {
            dbraw('add_user_group_data');
        }
    }

    public function revert()
    {
        if (schema()->hasTable('user_group')) {
            dbraw('add_user_group_data.rollback');
        }
    }
}
