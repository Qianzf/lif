<?php

namespace Lif\Cmd\Test;

class DemoFooBar extends \Lif\Cmd\CMD
{
    protected $intro = 'Test demo command';

    public function fire()
    {
        echo __CLASS__;
    }
}
