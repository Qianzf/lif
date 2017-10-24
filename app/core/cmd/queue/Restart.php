<?php

namespace Lif\Core\Cmd\Queue;

use Lif\Core\Abst\Command;

class Restart extends Command
{
    use \Lif\Core\Traits\QueueConfig;

    protected $intro = 'Restart LiF queue workers';

    public function fire()
    {
        dd($this->getQueue());
    }
}
