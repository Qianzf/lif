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
        share('hide-search-bar', true);

        view('ldtdf/bug/edit')->withBug($bug);
    }
}
