<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;

class Operator extends CtlBase
{
    public function index()
    {
        return view('ldtdf/operator/index');
    }
}
