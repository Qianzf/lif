<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User, Trending};

class Passport extends Ctl
{
    private $timeout = 3600;

    public function login()
    {
        if (share('__USER')) {
            redirect('/dep');
        }

        view('ldtdf/user/login');
    }

    public function auth(User $user, Trending $trend)
    {
        $lang     = $this->request->get('lang');
        $account  = $this->request->get('account');
        $passwd   = $this->request->get('passwd');
        $remember = $this->request->get('remember') ?? false;
        $user     = $user->login($account);

        if (! $user || !is_object($user)) {
            share('__error', sysmsg('NO_USER'));
            redirect('/dep/user/login');
        } elseif (! password_verify($passwd, $user->passwd)) {
            share('__error', sysmsg('ILLEGAL_USER_CREDENTIALS'));
            redirect('/dep/user/login');
        }

        unset($user->passwd);
        $timestamp = time();

        // Save login event to trending
        $trend->at     = date('Y-m-d H:i:s', $timestamp);
        $trend->uid    = $user->id;
        $trend->event  = 'LOGGEDIN';
        $trend->save();
        
        $shares = [
            '__USER'    => $user->items(),
            '__timeout' => $this->timeout,
            'system-roles' => [
                'ADMIN',
                'DEVELOPER',
                'TESTER',
            ],
        ];

        if ($lang) {
            $shares['__lang'] = $lang;
        }
        if ($remember) {
            $shares['__remember'] = $timestamp;
        }

        shares($shares);

        redirect('/dep');
    }

    public function logout()
    {
        session()->destory();

        redirect('/dep/user/login');
    }
}
