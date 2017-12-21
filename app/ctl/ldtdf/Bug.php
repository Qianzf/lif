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

        legal_or($querys, [
            'search'  => ['string', null],
            'creator' => ['int|min:1', null],
            'sort'    => ['ciin:desc,asc', 'desc'],
            'os'      => ['string', null],
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

        $users = $bug->getAllUsers();

        return view('ldtdf/bug/index')
        ->withBugsUsersOses(
            $bug->list(null, $where, true, $querys['sort']),
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),
            $this->getOses()
        )
        ->share('hide-search-bar', false);
    }

    public function info(BugModel $bug)
    {
        if (! $bug->alive()) {
            return redirect('/dep/bugs');
        }

        $user       = share('user.id');
        $editable   = ($bug->canEdit($user));
        $assignable = ($bug->canBeDispatchedBy($user));

        view('ldtdf/bug/info')
        ->withBugEditableAssignableTasksTrendings(
            $bug,
            $editable,
            $assignable,
            $bug->tasks(),
            $bug->trendings()
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
            '/dep/bugs',
            function () use ($bug, $user) {
                if (!$bug->alive() || $bug->creator != $user) {
                    return 'UPDATE_PERMISSION_DENIED';
                }
            },
            function () use ($bug, $user) {
                $bug->addTrending('update', $user);
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
