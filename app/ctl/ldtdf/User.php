<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User as UserModel, Trending};

class User extends Ctl
{
    public function trending(UserModel $user, Trending $trending)
    {
        $data = ($admin = ('ADMIN' === share('__USER.role')))
        ? $trending->list()
        : $user->find(share('__USER.id'))->trendings();

        view('ldtdf/user/trending')
        ->withAdminTrending($admin, $data);
    }

    public function profile($uid)
    {
        view('ldtdf/user/profile')->withUidEmail(
            share('__USER.id'),
            share('__USER.email')
        );
    }

    public function update(UserModel $user)
    {
        $request = $this->request->params;

        $user = $user->whereId(share('__USER.id'))->first();

        if (! $user) {
            session()->delete('__USER');
            share_error_i18n('NO_USER');
            return redirect('/dep/user/login');
        }

        $needUpdate = false;

        // If pass password then update it
        // If pass email and new email not equal to old email then update it
        if ($request->passwordNew) {
            if (! $request->passwordOld) {
                return sysmsg('PROVIDE_OLD_PASSWD');
            }
            if (! password_verify($request->passwordOld, $user->passwd)) {
                return sysmsg('ILLEGAL_OLD_PASSWD');
            }
            if (! password_verify($request->passwordNew, $user->passwd)) {
                $needUpdate = true;
                $user->passwd = password_hash(
                    $request->passwordNew,
                    PASSWORD_DEFAULT
                );
            }
        }
        if ($request->email && ($user->email != $request->email)) {
            $needUpdate = true;
            $user->email = $request->email;
        }

        $err = 'UPDATED_NOTHING';

        if ($needUpdate) {
            if ($user->save()) {
                unset($user->passwd);
                share('__USER', $user->items());

                $err = 'UPDATED_OK';

                if ($request->passwordNew) {
                    session()->delete('__USER');

                    redirect('/dep/user/login');
                }
            } else {
                $err = 'UPDATE_FAILED';
            }
        }

        share_error_i18n($err);

        redirect('/dep/user/profile');
    }
}
