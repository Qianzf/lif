<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddServerData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('server')) {
            dbraw('add_server_data');
        }
    }

    public function revert()
    {
        if (schema()->hasTable('server')) {
            dbraw('add_server_data.rollback');
        }
    }
}
