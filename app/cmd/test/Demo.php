<?php

namespace Lif\Cmd\Test;

class Demo extends \Lif\Cmd\CMD
{
    protected $intro = 'Test Command';

    public function fire()
    {
        echo __CLASS__;
    }
}
