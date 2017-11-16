<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateServerTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('server', function ($table) {
            // Definitions ...
        });
    }

    public function revert()
    {
        schema()->dropIfExists('server');
    }
}
