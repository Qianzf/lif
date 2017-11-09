<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Bug as BugModel;

class Bug extends Ctl
{
    public function index()
    {
        view('ldtdf/bug/index');
    }

    public function edit(BugModel $bug)
    {
        view('ldtdf/bug/edit')->withBug($bug);
    }
}
