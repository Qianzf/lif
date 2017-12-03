<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;

class Tool extends CtlBase
{
    public function index()
    {
        return view('ldtdf/tool/index');
    }
}
