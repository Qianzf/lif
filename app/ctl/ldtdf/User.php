<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User as UserModel, Trending};

class User extends Ctl
{
    public function todo()
    {
        view('ldtdf/user/todo');
    }

    public function info(UserModel $user)
    {
        share('hidden-search-bar', true);
        
        view('ldtdf/user/info')->withUser($user);
    }

    public function trending(UserModel $user, Trending $trending)
    {
        $pageScale = 16;
        $querys    = $this->request->all();
        $errs = legal_or($querys, [
            'page' => ['int|min:1', 1],
            'user' => ['int|min:0', 0],
            'search' => ['string', null],
        ]);

        $uid = (0 == $querys['user']) ? null : $querys['user'];
        if ($uid > 0) {
            $trending = $trending->whereUid($uid);
        }

        $takeFrom = ($querys['page'] - 1) * $pageScale;
        $data     = $trending->list([
            'user_id'   => $uid,
            'take_from' => $takeFrom,
            'take_cnt'  => $pageScale,
        ]);

        $users   = $user->listNonAdminUsers();
        $records = $trending->count();
        $pages   = ceil($records / $pageScale);

        view('ldtdf/user/trending')
        ->withUsersTrendingPagesRecords(
            $users,
            $data,
            $pages,
            $records
        );
    }

    public function profile($uid)
    {
        share('hidden-search-bar', true);
        
        view('ldtdf/user/profile')->withUidEmailName(
            share('user.id'),
            share('user.email'),
            share('user.name')
        );
    }

    public function update(UserModel $user)
    {
        $user = $user->whereId(share('user.id'))->first();

        if (! $user) {
            session()->delete('user');
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
                share('user', $user->items());

                $err = 'UPDATE_OK';

                if ($request->passwordNew) {
                    session()->delete('user');

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
