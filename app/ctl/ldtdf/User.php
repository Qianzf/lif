<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Web\Session;

class User extends Ctl
{
    public function profile($uid, Session $s)
    {
        $uid   = share('LOGGED_USER.id');
        $email = share('LOGGED_USER.email');

        view('ldtdf/user/profile')->withUidEmail($uid, $email);
    }

    public function update()
    {
        $request = $this->request->params;
        $uid     = share('LOGGED_USER.id');
        $email   = share('LOGGED_USER.email');
        $data    = [];

        // If pass password then update it
        // If pass email and new email not equal to old email then update it
        if ($request->password) {
            $data['passwd'] = password_hash(
                $request->password,
                PASSWORD_DEFAULT
            );
        } elseif ($request->email && ($email != $request->email)) {
            $data['email']  = $request->email;
        }

        if ($data) {
            $updateSuccess = db()
            ->table('user')
            ->whereId($uid)
            ->update($data);

            if ($updateSuccess) {
                share('LOGGED_USER', [
                    'id' => $uid,
                    'email' => $request->email,
                ]);

                $sysmsg = sysmsg('UPDATED_OK');
            } else {
                $sysmsg = sysmsg('UPDATE_FAILED');
            }
        } else {
            $sysmsg = sysmsg('UPDATED_NOTHING');
        }

        share('__error', $sysmsg);

        if ($request->password) {
            session()->delete('LOGGED_USER');
        }

        redirect('/dep/user/profile');
    }
}
