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
            'product' => ['int|min:0', null],
            'priority' => ['int|min:0', null],
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
            if (! empty_safe($product = $querys['product'])) {
                $where[] = ['product', $product];
            }
            if (! empty_safe($priority = $querys['priority'])) {
                $where[] = ['priority', $priority];
            }
        }

        $users   = $story->getAllUsers();
        $records = $story->count();
        $pages   = ceil($records / $pageScale);
        $querys['from'] = (($querys['page'] - 1) * $pageScale);
        $querys['take'] = $pageScale;
        $products = get_ldtdf_products();

        return view('ldtdf/story/index')
        ->withStoriesUsersPagesProductsRecordsPriorities(
            $story->list(null, $where, true, $querys),
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),
            $pages,
            ($products ? (array_combine(
                    array_column($products, 'id'),
                    array_column($products, 'name')
                )) : []
            ),
            $records,
            $this->getPriorities()
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

    protected function getPriorities() : array
    {
        return [0, 1, 2];
    }

    public function edit(StoryModel $story)
    {
        $principals = $story->getPrincipals([
            [db()->native('LOWER(`task`.`status`)'), '!=', 'canceled'],
        ]);

        return view('ldtdf/story/edit')
        ->withStoryAcceptancesDevelopersPrincipalsProductsEditablePriorities(
            $story,
            $story->getAcceptances(),
            get_ldtdf_devs(),
            ($principals ? array_column($principals, 'id') : []),
            get_ldtdf_products(),
            true,
            $this->getPriorities()
        );
    }

    public function create(StoryModel $story, Acceptance $acceptance)
    {
        if (! ($acceptances = $this->request->getCleanPost('acceptance'))) {
            share_error_i18n('MISSING_AC');
            
            return redirect($this->route);
        }

        $user = share('user.id');
        $developers = $this->request->getCleanPost('developers');

        $this->request->setPost('creator', $user);

        db()->start();

        return $this->responseOnCreated(
            $story,
            lrn('stories/?'),
            null,
            function ($status) use (
                $user,
                $story,
                $acceptance,
                $acceptances,
                $developers
            ) {
                if (ispint($status, false)
                    && ($acceptance->createFromOrigin(
                        'story',
                        $status,
                        $acceptances
                    ))
                ) {
                    if (true === create_tasks_when_create_origin(
                        $story, $developers
                    )) {
                        return db()->commit();
                    }
                }

                return db()->rollback();
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

        $user       = share('user.id');
        $developers = (array) $this->request->getCleanPost('developers');

        db()->start();

        return $this->responseOnUpdated(
            $story,
            null,
            function () use ($story, $user) {
                if ($story->alive() && ($story->creator != $user)) {
                    return 'UPDATE_PERMISSION_DENIED';
                }
            },
            function ($status) use (
                $story,
                $acceptance,
                $acceptances,
                $developers,
                $user
            ) {
                if (ispint($status) && (
                    $acceptance->updateFromOrigin(
                        'story',
                        $story->id,
                        $acceptances
                    )
                )) {
                    if (true === update_tasks_when_update_origin(
                        $story, $developers
                    )) {
                        return db()->commit();
                    }
                }

                return db()->rollback();
            }
        );
    }
}
