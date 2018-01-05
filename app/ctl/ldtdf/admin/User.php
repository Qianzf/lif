<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\User as UserModel;

class User extends Ctl
{
    protected $roles = null;
    
    public function __construct()
    {
        $this->roles = implode(',', share('system-roles'));
    }

    public function index(UserModel $user)
    {
        $request = $this->request->all();

        legal_or($request, [
            'search' => ['string', ''],
            'role'   => ["in:{$this->roles}", false],
            'page'   => ['int|min:1', 1],
            'status' => ['int|min:-1', -1],
        ]);

        if ($request['role']) {
            $user = $user->whereRole($request['role']);
        }
        if (-1 != $request['status']) {
            $user = $user->whereStatus($request['status']);   
        }

        if ($keyword = $request['search']) {
            // TODO: Virtual table && full text search
            $user = $user
            ->where(function ($table) use ($keyword) {
                $table
                ->whereAccount('like', '%'.$keyword.'%')
                ->orEmail('like', '%'.$keyword.'%')
                ->orName('like', '%'.$keyword.'%')
                ->whereStatus('!=', '-1');
            });
        }

        $offset  = 16;
        $start   = ($request['page'] - 1) * $offset;
        $records = $user->count();
        $users   = $user
        ->limit($start, $offset)
        ->get();
        $pages   = ceil(($records / $offset));

        view('ldtdf/admin/user/index')
        ->withUsers($users)
        ->withKeyword($keyword)
        ->withSearchrole($request['role'])
        ->withRecords($records)
        ->withPages($pages);
    }

    public function edit(UserModel $user)
    {
        return view('ldtdf/admin/user/edit')->withUser($user);
    }

    public function create(UserModel $user)
    {
        return $this->responseOnCreated(
            $user,
            lrn('admin/users/?'),
            function () {
                if ($this->request->has('passwd')) {
                    $this->request->setPost(
                        'passwd',
                        password_hash(
                            $this->request->has('passwd'),
                            PASSWORD_DEFAULT
                        )
                    );
                }
            }
        );
    }

    public function update(UserModel $user)
    {
        $needUpdate = false;
        $conflict   = [];
        $oldData    = $user->items();

        foreach ($this->request->posts() as $key => $value) {
            if ('passwd' == $key) {
                if ($value) {
                    $user->passwd = password_hash(
                        $value,
                        PASSWORD_DEFAULT
                    );
                    $needUpdate = true;
                }
            } else {
                if (isset($oldData[$key]) && ($oldData[$key] != $value)) {
                    if (in_array($key, ['account', 'email'])) {
                        // check the unicity of user's unique attribution
                        $conflict[] = [$key => $value];
                    }

                    $user->$key = $value;
                    $needUpdate = true;
                }
            }
        }

        if ($conflict && $user->hasConflict($conflict)) {
            share_error_i18n('USER_EMAIL_OR_ACCOUNT_CONFLICT');
            return redirect($this->route);
        }

        $sysmsg = 'UPDATED_NOTHING';

        if ($needUpdate) {
            if (ispint($err = $user->save())) {
                share_error_i18n('UPDATED_OK');
                // Check if update self
                if ($user->id == share('user.id')) {
                    if ($user->status == 1) {
                        share('user', $user->items());
                    } else {
                        session()->destory();
                        return redirect(lrn('users/login'));
                    }
                }
            } else {
                share_error(L('UPDATE_FAILED'), $err);
            }
        }

        return redirect('/dep/admin/users');
    }
}
