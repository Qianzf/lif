<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\UserGroup as Group;
use Lif\Mdl\User;

class UserGroup extends Ctl
{
    public function index(Group $group)
    {
        view('ldtdf/admin/user/group/index')
        ->withRecordsGroups(
            $group->count(),
            $group->all()
        );
    }

    public function add(Group $group, User $user)
    {
        share('hide-search-bar', true);

        return view('ldtdf/admin/user/group/edit')
        ->withGroupUsers(
            $group,
            $user->whereStatus(1)->all()
        );
    }

    public function edit(Group $group, User $user)
    {
        $error = $back2last = null;
        if (! $group->alive()) {
            $error     = L('GROUP_NOT_EXISTS');
            $back2last = share('url_previous');
        }

        shares([
            'hide-search-bar' => true,
            '__error'   => $error,
            'back2last' => $back2last,
        ]);
        
        return view('ldtdf/admin/user/group/edit')
        ->withGroupUsers(
            $group,
            $user->whereStatus(1)->all()
        );
    }

    public function create(Group $group)
    {
        // Check if group name already exsits
        if (($name = $this->request->get('name'))
            && $group->hasSameGroup($name)
        ) {
            share_error(L('GROUP_ALREADY_EXISTS', $name));

            return redirect($this->route);
        }

        if (($status = $group->createWithUsers($this->request->posts)) > 0) {
            share_error_i18n('CREATED_SUCCESS');
        } else {
            $status = 'new';
            share_error_i18n('CREATE_FAILED');
        }

        return redirect("admin/users/groups/{$status}");
    }

    public function update(Group $group)
    {
        // Check if group name already exsits
        if (($name = $this->request->get('name'))
            && (strtoupper($group->name) !==  strtoupper($name))
            && $group->hasSameGroup($name)
        ) {
            share_error(L('GROUP_ALREADY_EXISTS', $name));
        } else {
            $msg = $group->updateWithUsers($this->request->posts)
            ? 'UPDATED_OK' : 'UPDATE_FAILED';

            share_error_i18n($msg);
        }

        return redirect($this->route);
    }
}
