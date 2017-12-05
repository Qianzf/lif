<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Seed extends Command
{

    use \Lif\Core\Traits\Ditable;

    protected $intro = 'Commit new-added database seeds';

    public function fire()
    {
        $this->commit('dbseed');
    }
}
