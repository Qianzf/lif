<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\Bug as BugModel;

class Bug extends Ctl
{
    public function index(BugModel $bug)
    {
        return view('ldtdf/bug/index')->withBugs($bug->all());
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
        if ($bug->creator != share('user.id')) {
            share_error_i18n('UPDATE_PERMISSION_DENIED');
            redirect($this->route);
        }

        if (! $bug->alive()) {
            share_error_i18n('NO_BUG');
            redirect('/dep/bugs');
        }

        if (!empty_safe($err = $bug->save($this->request->posts))
            && is_numeric($err)
            && ($err >= 0)
        ) {
            if ($err > 0) {
                $status = 'UPDATE_OK';
                $bug->addTrending('update');
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

    public function create(BugModel $bug)
    {
        $data = $this->request->posts();

        $data['creator'] = share('user.id');

        if (($status = $bug->create($data))
            && is_integer($status)
            && ($status > 0)
        ) {
            $msg = 'CREATED_SUCCESS';
            $bug->addTrending('create', $data['creator']);
        } else {
            share_error_i18n(L('CREATED_FAILED', L($status)));

            return $this->add($story->setItems($this->request->posts));
        }

        share_error_i18n($msg);

        return redirect("/dep/bugs/{$status}");
    }
}
