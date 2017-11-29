<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User as UserModel, Trending};

class User extends Ctl
{
    public function todo(UserModel $user)
    {
        if (! ($user = $user->find(share('user.id')))) {
            share_error_i18n('NO_USER');

            return redirect($this->route);
        }

        return view('ldtdf/user/todo')->withTasks($user->getCurrentTasks());
    }

    public function list(UserModel $user)
    {
        $where[] = ['role', '!=', 'admin'];

        if ($search = $this->request->get('search')) {
            $where[] = ['name', 'like', "%{$search}%"];
        }

        return response($user->list(['id', 'name'], $where, false));
    }

    public function info(UserModel $user)
    {
        $error = $back2last = null;
        if (! $user->isAlive()) {
            $error     = lang('NO_USER');
            $back2last = share('url_previous');
        }

        shares([
            'hide-search-bar' => true,
            '__error'   => $error,
            'back2last' => $back2last,
        ]);
        
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

        $uid = (0 === $querys['user']) ? null : $querys['user'];
        if ($uid > 0) {
            $trending = $trending->whereUser($uid);
        }

        $from = ($querys['page'] - 1) * $pageScale;
        $trendings = $trending->list([
            'user' => $uid,
            'from' => $from,
            'take' => $pageScale,
        ]);

        $users   = $user->getNonAdmin();
        $records = $trending->count();
        $pages   = ceil($records / $pageScale);

        view('ldtdf/user/trending')
        ->withUsersTrendingsPagesRecords(
            $users,
            $trendings,
            $pages,
            $records
        );
    }

    public function profile($uid)
    {
        share('hide-search-bar', true);
        
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
