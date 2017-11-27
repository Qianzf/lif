<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Story as StoryModel;
use Lif\Mdl\Project;

class Story extends Ctl
{
    public function index()
    {
        view('ldtdf/story/index');
    }

    public function info(StoryModel $story)
    {
        view('ldtdf/story/info');
    }

    public function edit(StoryModel $story)
    {
        view('ldtdf/story/edit')
        ->withStoryEditable($story, false);
    }

    public function add(StoryModel $story)
    {
        view('ldtdf/story/edit')
        ->withStoryEditable($story, true);
    }

    public function create(StoryModel $story)
    {
        if (($status = $story->create($this->request->all()))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
            $story->addTrending('create');
        } else {
            $msg    = lang('CREATED_FAILED', $status);
            $status = 'new';
        }

        share_error_i18n($msg);

        return redirect("/dep/stories/{$status}");
    }
}
