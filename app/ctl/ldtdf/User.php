<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\User as UserModel;

class User extends Ctl
{
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
            share('__error', sysmsg('NO_USER'));
            return redirect('/dep/user/login');
        }

        // If pass password then update it
        // If pass email and new email not equal to old email then update it
        if ($request->passwordNew) {
            if (! $request->passwordOld) {
                return sysmsg('PROVIDE_OLD_PASSWD');
            }
            if (! password_verify($request->passwordOld, $user->passwd)) {
                return sysmsg('ILLEGAL_OLD_PASSWD');
            }

            $user->passwd = password_hash(
                $request->passwordNew,
                PASSWORD_DEFAULT
            );
        } elseif ($request->email && ($user->email != $request->email)) {
            $user->email  = $request->email;
        }

        $sysmsg = sysmsg('UPDATE_FAILED');

        if ($user->save()) {
            unset($user->passwd);
            share('__USER', $user->items());

            $sysmsg = sysmsg('UPDATED_OK');

            if ($request->passwordNew) {
                session()->delete('__USER');
            }
        }

        share('__error', $sysmsg);

        redirect('/dep/user/profile');
    }
}
