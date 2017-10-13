<?php

namespace Lif\Cmd;

class Patch extends Command
{
    protected $intro = 'Patch command';

    public function fire(?array $params)
    {
        dd(subsets([
            'Help',
            'Test',
            'Demo',
        ]));
    }
}
