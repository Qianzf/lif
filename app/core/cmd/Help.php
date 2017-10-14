<?php

namespace Lif\Core\Cmd;

class Help extends Command
{
    protected $intro = 'Output help messages of CLI LiF';

    public function fire()
    {
        return $this->help();
    }
}
