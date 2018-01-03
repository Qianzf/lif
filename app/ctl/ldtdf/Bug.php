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
            'sort'    => ['ciin:desc,asc', 'desc'],
            'os'      => ['string|notin:-1', null],
            'page'    => ['int|min:1', 1],
        ]);

        if ($search = $querys['search']) {
            $where[] = ['title', 'like', "%{$search}%"];
        }
        if ($creator = $querys['creator']) {
            $where[] = ['creator', $creator];
        }
        if ($os = $querys['os']) {
            $where[] = [db()->native('LOWER(`os`)'), strtolower($os)];
        }

        $users   = $bug->getAllUsers();
        $records = $bug->count();
        $pages   = ceil($records / $pageScale);
        $querys['from'] = (($querys['page'] - 1) * $pageScale);
        $querys['take'] = $pageScale;

        return view('ldtdf/bug/index')
        ->withBugsUsersOsesPagesRecords(
            $bug->list(null, $where, true, $querys),
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),
            $this->getOses(),
            $pages,
            $records
        )
        ->share('hide-search-bar', false);
    }

    public function info(BugModel $bug)
    {
        if (! $bug->alive()) {
            return redirect('/dep/bugs');
        }

        $querys     = $this->request->gets();
        legal_or($querys, [
            'trending' => ['ciin:desc,asc', 'desc'],
        ]);
        $user       = share('user.id');
        $editable   = ($bug->canEdit($user));
        $assignable = ($bug->canBeDispatchedBy($user));

        view('ldtdf/bug/info')
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
        share('hide-search-bar', true);

        view('ldtdf/bug/edit')->withBugEditableOses(
            $bug,
            true,
            $this->getOses()
        );
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

        return $this->responseOnUpdated(
            $bug,
            null,
            function () use ($bug, $user) {
                if (!$bug->alive() || ($bug->creator != $user)) {
                    return 'UPDATE_PERMISSION_DENIED';
                }
            },
            function ($status) use ($bug, $user) {
                if (ispint($status, false)) {
                    $bug->addTrending('update', $user);
                }
            }
        );
    }

    public function create(BugModel $bug)
    {
        $user = share('user.id');

        $this->request->setPost('creator', $user);

        return $this->responseOnCreated(
            $bug,
            '/dep/bugs/?',
            null,
            function () use ($bug, $user) {
                $bug->addTrending('create', $user);
            }
        );
    }
}
