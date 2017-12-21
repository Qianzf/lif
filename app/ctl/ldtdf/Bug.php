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
            'search' => ['string', null],
        ]);

        if ($search = $querys['search']) {
            $where[] = ['title', 'like', "%{$search}%"];
        }

        return view('ldtdf/bug/index')
        ->withBugs($bug->list(null, $where))
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
