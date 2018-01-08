<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Story as StoryModel;
use Lif\Mdl\{Project, Acceptance};

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
        $pageScale = 16;

        legal_or($querys, [
            'search'  => ['string', null],
            'id'      => ['int|min:1', null],
            'creator' => ['int|min:1', null],
            'sort'    => ['ciin:desc,asc', 'desc'],
            'page'    => ['int|min:1', 1],
        ]);

        if ($id = ($querys['id'] ?? false)) {
            $where[] = ['id', $id];
        } else {
            if ($search = $querys['search']) {
                $where[] = ['title', 'like', "%{$search}%"];
            }
            if ($creator = $querys['creator']) {
                $where[] = ['creator', $creator];
            }
        }

        $users   = $story->getAllUsers();
        $records = $story->count();
        $pages   = ceil($records / $pageScale);
        $querys['from'] = (($querys['page'] - 1) * $pageScale);
        $querys['take'] = $pageScale;

        return view('ldtdf/story/index')
        ->withStoriesUsersPagesRecords(
            $story->list(null, $where, true, $querys),
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),
            $pages,
            $records
        )
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
            return redirect(lrn('stories'));
        }

        $querys = $this->request->gets();
        
        legal_or($querys, [
            'trending' => ['ciin:desc,asc', 'desc'],
        ]);

        $user       = share('user.id');
        $editable   = $story->canBeEditedBy($user);
        $assignable = $story->canBeDispatchedBy($user);

        view('ldtdf/story/info')
        ->withStoryAcceptancesEditableAssignableTasksTrendings(
            $story,
            $story->getAcceptances(),
            $editable,
            $assignable,
            $story->tasks(),
            $story->trendings($querys)
        );
    }

    public function edit(StoryModel $story)
    {
        return view('ldtdf/story/edit')
        ->withStoryAcceptancesEditable(
            $story,
            $story->getAcceptances(),
            true
        );
    }

    public function create(StoryModel $story, Acceptance $acceptance)
    {
        db()->start();

        if (! ($acceptances = $this->request->getCleanPost('acceptance'))) {
            share_error_i18n('MISSING_AC');
            
            return redirect($this->route);
        }

        $this->request->setPost('creator', share('user.id'));

        return $this->responseOnCreated(
            $story,
            lrn('stories/?'),
            null,
            function ($status) use ($story, $acceptance, $acceptances) {
                if (ispint($status, false)
                    && ($acceptance->createFromOrigin(
                        'story',
                        $status,
                        $acceptances
                    ))
                ) {
                    $story->addTrending('create', $story->creator);

                    db()->commit();
                } else {
                    db()->rollback();
                }
            }
        );
    }

    public function updateAC(StoryModel $story, Acceptance $acceptance)
    {
        $status = 0;

        if ($story->alive()
            && $acceptance->alive()
            && ($acceptance->whose  == 'story')
            && ($acceptance->origin == $story->id)
        ) {
            $acceptance->status = (
                'true' == $this->request->posts('checked')
            ) ? 'checked' : null;

            $status = $acceptance->save();
        }

        return response([
            'res' => $status,
        ]);
    }

    public function update(StoryModel $story, Acceptance $acceptance)
    {
        if (! ($acceptances = $this->request->getCleanPost('acceptance'))) {
            share_error_i18n('MISSING_AC');
            
            return redirect("{$this->route}/edit");
        }

        return $this->responseOnUpdated(
            $story,
            null,
            function () use ($story) {
                if ($story->alive() && ($story->creator != share('user.id'))) {
                    share_error_i18n('UPDATE_PERMISSION_DENIED');
                    redirect($this->route);
                }
            },
            function ($status) use ($story, $acceptance, $acceptances) {
                if (($status > 0) || (
                    $acceptance->updateFromOrigin(
                        'story',
                        $story->id,
                        $acceptances
                    )
                )) {
                    share_error_i18n('UPDATE_OK');

                    $story->addTrending('update', $story->creator);
                }
            }
        );
    }
}
