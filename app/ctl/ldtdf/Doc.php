<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Doc as DocModel;

class Doc extends Ctl
{
    public function index()
    {
        view('ldtdf/docs/index');
    }

    public function add(DocModel $doc)
    {
        view('ldtdf/docs/edit')
        ->withDocEditable($doc, true);
    }
}
