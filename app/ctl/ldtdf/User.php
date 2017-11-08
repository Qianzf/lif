<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User as UserModel, Trending};

class User extends Ctl
{
    public function trending(UserModel $user, Trending $trending)
    {
        $pageScale = 16;
        $querys    = $this->request->all();
        $pages     = ceil($trending->count() / $pageScale);

        $errs = legal_or($querys, [
            'page' => ['int|min:1|max:'.$pages, 1],
        ]);

        $takeFrom  = ($querys['page'] - 1) * $pageScale;
        $data      = ($admin = ('ADMIN' === share('__USER.role')))
        ? $trending->list([
            'take_from' => $takeFrom,
            'take_cnt'  => $pageScale,
        ])
        : $user->find(share('__USER.id'))->trendings();
        $records = count($data);

        view('ldtdf/user/trending')
        ->withAdminTrendingPagesRecords(
            $admin,
            $data,
            $pages,
            $records
        );
    }

    public function profile($uid)
    {
        view('ldtdf/user/profile')->withUidEmailName(
            share('__USER.id'),
            share('__USER.email'),
            share('__USER.name')
        );
    }

    public function update(UserModel $user)
    {
        $user = $user->whereId(share('__USER.id'))->first();

        if (! $user) {
            session()->delete('__USER');
            share_error_i18n('NO_USER');
            return redirect('/dep/user/login');
        }

        $request    = $this->request->params;
        $oldData    = $user->items();
        $needUpdate = false;
        $conflict   = [];

        foreach ($request as $key => $value) {
            if (isset($oldData[$key]) && ($user->$key != $value)) {
                if ('passwordNew' == $key) {
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
                    continue;
                }
                if (in_array($key, ['account', 'email'])) {
                    // check the unicity of user's unique attribution
                    $conflict[] = [$key => $value];
                }

                $needUpdate = true;
                $user->$key = $value;
            }
        }

        if ($conflict && $user->hasConflict($conflict)) {
            share_error_i18n('USER_EMAIL_OR_ACCOUNT_CONFLICT');
            return redirect($this->route);
        }

        $err = 'UPDATED_NOTHING';

        if ($needUpdate) {
            if ($user->save()) {
                unset($user->passwd);
                share('__USER', $user->items());

                $err = 'UPDATE_OK';

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
