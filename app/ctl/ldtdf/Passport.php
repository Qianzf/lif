<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User, Trending};

class Passport extends Ctl
{
    public function login()
    {
        if (share('__USER')) {
            redirect(route('dep'));
        }

        view('ldtdf/user/login');
    }

    public function loginAction(User $u, Trending $trend)
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

        // Save login event to trending
        $trend->at     = date('Y-m-d H:i:s');
        $trend->uid    = $user->id;
        $trend->event  = 'LOGGEDIN';
        $trend->save();

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
