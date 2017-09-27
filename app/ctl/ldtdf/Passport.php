<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\User;

class Passport extends Ctl
{
    public function login()
    {
        if (share('__USER')) {
            redirect(route('dep'));
        }

        view('ldtdf/user/login');
    }

    public function loginAction(User $u)
    {
        $lang    = $this->request->get('lang');
        $account = $this->request->get('account');
        $passwd  = $this->request->get('passwd');
        $user    = $u
        ->whereStatus(1)
        ->where(function ($user) use ($account) {
            $user
            ->whereAccount($account)
            ->orEmail($account);
        })
        ->first();

        if (! $user || !is_object($user)) {
            share('__error', sysmsg('NO_USER'));
            redirect('/dep/user/login');
        } elseif (! password_verify($passwd, $user->passwd)) {
            share('__error', sysmsg('ILLEGAL_USER_CREDENTIALS'));
            redirect('/dep/user/login');
        }

        unset($user->passwd);

        share('__USER', $user->items());
        share('system-roles', [
            'ADMIN',
            'DEVELOPER',
            'TESTER',
        ]);
        
        if ($lang) {
            share('__lang', $lang);
        }

        redirect(route('dep'));
    }

    public function logout()
    {
        session()->destory();

        redirect('/dep/user/login');
    }
}
