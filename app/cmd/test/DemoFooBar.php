<?php

namespace Lif\Cmd\Test;

class DemoFooBar extends \Lif\Cmd\Command
{
    protected $intro = 'Test demo command';

    public function fire(? array $params)
    {
        echo __CLASS__;
    }
}
