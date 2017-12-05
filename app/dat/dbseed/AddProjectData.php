<?php

namespace Lif\Dat\Dbseed;

use Lif\Core\Storage\Dit;

class AddProjectData extends Dit
{
    public function commit()
    {
        if (schema()->hasTable('project')) {
            dbraw('add_project_data');
        }
    }

    public function revert()
    {
        if (schema()->hasTable('project')) {
            dbraw('add_project_data.rollback');
        }
    }
}
