<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateDocTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('doc', function ($table) {
            $table->pk('id');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('doc');
    }
}
