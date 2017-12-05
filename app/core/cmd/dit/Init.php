<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Init extends Command
{
    protected $intro = 'Initialize LiF dit';

    protected $option = [
        '-F' => 'force',
        '--force' => 'force',
    ];

    protected $desc = [
        'force' => 'Force init Dit regardless current dit status',
    ];

    private $force = false;

    public function fire()
    {
        if ($this->force) {
            schema()->dropIfExists('__dit__');
        }

        init_dit_table();
    }

    public function force()
    {
        $this->force = true;
    }
}
