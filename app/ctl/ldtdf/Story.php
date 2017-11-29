<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Story as StoryModel;
use Lif\Mdl\Project;

class Story extends Ctl
{
    public function index(StoryModel $story)
    {
        view('ldtdf/story/index')->withStories($story->list());
    }

    public function list(StoryModel $story)
    {
        $where = [];
        
        if ($search = $this->request->get('search')) {
            $where[] = ['title', 'like', "%{$search}%"];
        }

        return response($story->list(['id', 'title'], $where, false));
    }

    public function info(StoryModel $story)
    {
        if (! $story->isAlive()) {
            return redirect('/dep/stories');
        }

        $user       = share('user.id');
        $editable   = ($story->canEdit());
        $assignable = ($story->canBeDispatchedBy($user));

        view('ldtdf/story/info')
        ->withStoryEditableAssignableTasksTrendings(
            $story,
            $editable,
            $assignable,
            $story->tasks(),
            $story->trendings()
        );
    }

    public function edit(StoryModel $story)
    {
        view('ldtdf/story/edit')
        ->withStoryEditable($story, true);
    }

    public function add(StoryModel $story)
    {
        view('ldtdf/story/edit')
        ->withStoryEditable($story, true);
    }

    public function create(StoryModel $story)
    {
        $data = $this->request->all();
        $data['creator'] = share('user.id');

        if (($status = $story->create($data))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
            $story->addTrending('create');
        } else {
            share_error_i18n(lang('CREATED_FAILED', lang($status)));

            return $this->add($story->setItems($this->request->posts));
        }

        share_error_i18n($msg);

        return redirect("/dep/stories/{$status}");
    }

    public function update(StoryModel $story)
    {
        if ($story->creator != share('user.id')) {
            share_error_i18n('UPDATE_PERMISSION_DENIED');
            redirect($this->route);
        }

        if (! $story->isAlive()) {
            share_error_i18n('TASK_NOT_FOUND');
            redirect('/dep/tasks');
        }

        if (!empty_safe($err = $story->save($this->request->posts))
            && is_numeric($err)
            && ($err >= 0)
        ) {
            if ($err > 0) {
                $status = 'UPDATE_OK';
                $story->addTrending('update');
            } else {
                $status = 'UPDATED_NOTHING';
            }
        } else {
            $status = 'UPDATE_FAILED';
        }

        $err = is_integer($err) ? null : lang($err);

        share_error(lang($status, $err));

        redirect($this->route);
    }
}
