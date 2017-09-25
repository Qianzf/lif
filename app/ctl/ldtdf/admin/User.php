<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\User as UserModel;

class User extends Ctl
{
    public function index(UserModel $user)
    {
        view('ldtdf/admin/users')->withUsers($user->limit(16)->get());   
    }

    public function info(UserModel $user)
    {
        share('system-roles', [
            'ADMIN',
            'DEVELOPER',
            'TESTER',
        ]);

        view('ldtdf/admin/users/edit')->withUser($user);
    }

    public function add(UserModel $user)
    {
        $this->validate($this->request->all(), [
            'account' => 'need',
            'name'    => 'need',
            'email'   => 'need|email',
            'passwd'  => 'need',
            'role'    => 'need|in:ADMIN,TESTER,DEVELOPER',
        ]);

        foreach ($this->request->all() as $key => $value) {
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
