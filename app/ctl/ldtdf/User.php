<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User as UserModel, Trending};

class User extends Ctl
{
    public function trending(UserModel $user, Trending $trending)
    {
        $pageScale = 20;
        $querys    = $this->request->all();
        $pages     = ceil($trending->count() / $pageScale);

        $errs = legal_or($querys, [
            'page' => ['int|min:1|max:'.$pages, 1],
        ]);

        if (isset($errs['page']) && true !== $errs['page']) {
            share_error_i18n($errs['page']);

            return redirect($this->route);
        }

        $takeFrom  = ($querys['page'] - 1) * $pageScale;
        $data      = ($admin = ('ADMIN' === share('__USER.role')))
        ? $trending->list([
            'take_from' => $takeFrom,
            'take_cnt'  => $pageScale,
        ])
        : $user->find(share('__USER.id'))->trendings();

        view('ldtdf/user/trending')
        ->withAdminTrendingPages($admin, $data, $pages);
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
