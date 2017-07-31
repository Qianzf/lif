<?php

namespace Lif\Core\Strategy;

use Lif\Core\Intf\Strategy;
use Lif\Core\Abst\Container;

class Cli extends Container implements Strategy
{
    public $argvs = null;

    public function fire()
    {
        dd($GLOBALS);
        dd(get_included_files());
        dd($this->argvs);

        return $this;
    }

    public function setArgvs($argvs)
    {
        $this->argvs = $argvs;

        return $this;
    }
}
