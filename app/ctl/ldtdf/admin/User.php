<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\User as UserModel;

class User extends Ctl
{
    public function index(UserModel $user)
    {
        $request = $this->request->all();

        legal_or($request, [
            'search' => ['string', ''],
            'role'   => ['in:ADMIN,DEVELOPER,TESTER', false],
            'page'   => ['int|min:1', 1],
        ]);

        $offset = 16;
        $start  = ($request['page'] - 1) * $offset;

        if ($request['role']) {
            $user = $user->whereRole($request['role']);
        }
        if ($keyword = $request['search']) {
            // TODO: Virtual table && full text search
            $user = $user->where(function ($table) use ($keyword) {
                $table
                ->whereAccount('like', '%'.$keyword.'%')
                ->orEmail('like', '%'.$keyword.'%')
                ->orName('like', '%'.$keyword.'%');
            });
        }

        $users = $user
        ->limit($start, $offset)
        ->get();

        view('ldtdf/admin/users')
        ->withUsers($users)
        ->withKeyword($keyword)
        ->withSearchrole($request['role']);
    }

    public function info(UserModel $user)
    {
        view('ldtdf/admin/users/edit')->withUser($user);
    }

    public function add(UserModel $user)
    {
        $request = $this->request->all();

        $this->validate($request, [
            'account' => 'need',
            'name'    => 'need',
            'email'   => 'need|email',
            'passwd'  => 'need',
            'role'    => 'need|in:ADMIN,TESTER,DEVELOPER',
        ]);

        foreach ($request as $key => $value) {
            if ('passwd' == $key) {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }

            $user->$key = $value;
        }

        if ($user->save()) {
            share('__error', sysmsg('CREATED_SUCCESS'));

            redirect(route('dep.admin.users'));
        }

        share('__error', sysmsg('CREATE_FAILED'));
        return redirect('/'.$this->route);
    }

    public function update(UserModel $user)
    {
        $needUpdate = false;

        $oldData = $user->items();

        foreach ($this->request->all() as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                if ('passwd' == $key) {
                    if ($value) {
                        $needUpdate = true;
                        $user->passwd = password_hash(
                            $value,
                            PASSWORD_DEFAULT
                        );
                    }
                } else {
                    $needUpdate = true;
                    $user->$key = $value;
                }
            }
        }

        $sysmsg = 'UPDATED_NOTHING';

        if ($needUpdate) {
            if ($user->save()) {
                $sysmsg =  'UPDATED_OK';
                // Check if update self
                if ($user->id == share('__USER.id')) {
                    share('__USER', $user->data());
                }
            } else {
                $sysmsg =  'UPDATE_FAILED';
            }
        }

        share('__error', sysmsg($sysmsg));

        redirect(route('dep.admin.users'));
    }

    public function delete(UserModel $user)
    {
        $err = 'DELETED_FAILED';

        $deleteUserID = $user->id;

        if ($user->delete()) {
            $err = 'DELETED_OK';
            // Check if delete self
            if ($deleteUserID == share('__USER.id')) {
                session()->delete('__USER');
            }
        }

        share('__error', sysmsg($err));

        redirect(route('dep.admin.users'));
    }
}
