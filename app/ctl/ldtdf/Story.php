<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Story as StoryModel;
use Lif\Mdl\Project;

class Story extends Ctl
{
    public function __construct()
    {
        share('hide-search-bar', true);
    }

    public function index(StoryModel $story)
    {
        $querys = $this->request->gets();
        $where  = [];

        legal_or($querys, [
            'search' => ['string', null],
        ]);

        if ($search = $querys['search']) {
            $where[] = ['title', 'like', "%{$search}%"];
        }

        return view('ldtdf/story/index')
        ->withStories($story->list(null, $where))
        ->share('hide-search-bar', false);
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
        if (! $story->alive()) {
            return redirect('/dep/stories');
        }

        $user       = share('user.id');
        $editable   = $story->canBeEditedBy($user);
        $assignable = $story->canBeDispatchedBy($user);

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
        return view('ldtdf/story/edit')
        ->withStoryEditable($story, true);
    }

    public function create(StoryModel $story)
    {
        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated($story, '/dep/stories/?');
    }

    public function update(StoryModel $story)
    {
        if ($story->creator != share('user.id')) {
            share_error_i18n('UPDATE_PERMISSION_DENIED');
            redirect($this->route);
        }

        if (! $story->alive()) {
            share_error_i18n('TASK_NOT_FOUND');
            redirect('/dep/tasks');
        }

        if (!empty_safe($err = $story->save($this->request->posts()))
            && is_numeric($err)
            && ($err >= 0)
        ) {
            if ($err > 0) {
                $status = 'UPDATE_OK';
                $story->addTrending('update', $story->creator);
            } else {
                $status = 'UPDATED_NOTHING';
            }
        } else {
            $status = 'UPDATE_FAILED';
        }

        $err = is_integer($err) ? null : L($err);

        share_error(L($status, $err));

        redirect($this->route);
    }
}
