<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddUserGroupMapData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('user_group_map')) {
            dbraw('add_user_group_map_data');
        }
    }

    public function revert()
    {
        if (schema()->hasTable('user_group_map')) {
            dbraw('add_user_group_map_data.rollback');
        }
    }
}
