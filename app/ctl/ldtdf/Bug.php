<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Bug as BugModel;

class Bug extends Ctl
{
    public function __construct()
    {
        share('hide-search-bar', true);
    }

    public function index(BugModel $bug)
    {
        $querys = $this->request->gets();
        $where  = [];
        $pageScale = 16;

        legal_or($querys, [
            'search'  => ['string', null],
            'creator' => ['int|min:1', null],
            'product' => ['int|min:0', null],
            'sort'    => ['ciin:desc,asc', 'desc'],
            'os'      => ['string|notin:-1', null],
            'page'    => ['int|min:1', 1],
            'id'      => ['int|min:1', null],
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
            if ($os = $querys['os']) {
                $where[] = [db()->native('LOWER(`os`)'), strtolower($os)];
            }
        }

        $users   = $bug->getAllUsers();
        $records = $bug->count();
        $pages   = ceil($records / $pageScale);
        $querys['from'] = (($querys['page'] - 1) * $pageScale);
        $querys['take'] = $pageScale;
        $products       = get_ldtdf_products();

        return view('ldtdf/bug/index')
        ->withBugsUsersOsesPagesProductsRecords(
            $bug->list(null, $where, true, $querys),
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),
            $this->getOses(),
            $pages,
            ($products ? (array_combine(
                    array_column($products, 'id'),
                    array_column($products, 'name')
                )) : []
            ),
            $records
        )
        ->share('hide-search-bar', false);
    }

    public function info(BugModel $bug)
    {
        if (! $bug->alive()) {
            return redirect(lrn('bugs'));
        }

        $querys     = $this->request->gets();
        legal_or($querys, [
            'trending' => ['ciin:desc,asc', 'desc'],
        ]);
        $user       = share('user.id');
        $editable   = ($bug->canEdit($user));
        $assignable = ($bug->canBeDispatchedBy($user));

        return view('ldtdf/bug/info')
        ->withBugEditableAssignableTasksTrendings(
            $bug,
            $editable,
            $assignable,
            $bug->tasks(),
            $bug->trendings($querys)
        );
    }

    public function edit(BugModel $bug)
    {
        $principals = $bug->getPrincipals([
            [db()->native('LOWER(`task`.`status`)'), '!=', 'canceled'],
        ]);

        return view('ldtdf/bug/edit')
        ->withBugEditableOsesPrincipalsProductsDevelopers(
            $bug,
            true,
            $this->getOses(),
            ($principals ? array_column($principals, 'id') : []),
            get_ldtdf_products(),
            get_ldtdf_devs()
        )
        ->share('hide-search-bar', true);
    }

    private function getOses()
    {
        return [
            'Windows',
            'Linux',
            'macOS',
            'iOS',
            'Android',
            'Others',
        ];
    }

    public function update(BugModel $bug)
    {
        $user = share('user.id');
        $developers = (array) $this->request->getCleanPost('developers');

        db()->start();

        return $this->responseOnUpdated(
            $bug,
            null,
            function () use ($bug, $user) {
                if (!$bug->alive() || ($bug->creator != $user)) {
                    return 'UPDATE_PERMISSION_DENIED';
                }
            },
            function ($status) use ($bug, $user, $developers) {
                if (ispint($status)) {
                    if (true === update_tasks_when_update_origin(
                        $bug, $developers
                    )) {
                        return db()->commit();
                    }
                }

                return db()->rollback();
            }
        );
    }

    public function create(BugModel $bug)
    {
        $user       = share('user.id');
        $developers = $this->request->getCleanPost('developers');

        $this->request->setPost('creator', $user);

        db()->start();

        return $this->responseOnCreated(
            $bug,
            lrn('bugs/?'),
            null,
            function ($status) use ($bug, $user, $developers) {
                if (ispint($status, false)) {
                    if (true === create_tasks_when_create_origin(
                        $bug, $developers
                    )) {
                        return db()->commit();
                    }
                }

                return db()->rollback();
            }
        );
    }
}
