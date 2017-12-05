<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Seed extends Command
{

    use \Lif\Core\Traits\Ditable;

    protected $intro = 'Commit new-added database seed';

    public function fire()
    {
        $this->__commit('dbseed');
    }
}
