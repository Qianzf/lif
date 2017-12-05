<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Commit extends Command
{
    use \Lif\Core\Traits\Ditable;

    protected $intro = 'Commit new-added database schemas';

    public function fire()
    {
        $this->commit('dbvc');
    }
}
