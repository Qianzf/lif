<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Story as StoryModel;

class Story extends Ctl
{
    public function index()
    {
        view('ldtdf/story/index');
    }

    public function add(StoryModel $story)
    {
        view('ldtdf/story/edit')
        ->withStoryEditable($story, true);
    }
}
