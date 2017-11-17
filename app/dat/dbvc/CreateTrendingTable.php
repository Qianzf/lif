<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateTrendingTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('trending', function ($table) {
            $table->pk('id');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('trending');
    }
}
