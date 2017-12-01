<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Mdl\{User, Trending};

class Passport extends Ctl
{
    private $timeout = 3600;

    public function login()
    {
        if (share('user')) {
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

        if (!$user || !is_object($user)) {
            share('__error', sysmsg('NO_USER'));
            redirect('/dep/users/login');
        } elseif (! password_verify($passwd, $user->passwd)) {
            share('__error', sysmsg('ILLEGAL_USER_CREDENTIALS'));
            redirect('/dep/users/login');
        }
        
        unset($user->passwd);
        $timestamp = time();
        
        $shares = [
            'user'    => $user->items(),
            'timeout' => $this->timeout,
            'system-roles' => [
                'admin',
                'pm',
                'dev',
                'ops',
                'test',
                'ui',
            ],
        ];

        if ($lang) {
            $shares['__lang'] = $lang;
        }
        if ($remember) {
            $shares['remember'] = $timestamp;
        }

        shares($shares);

        $trend->add('login_sys');

        redirect('/dep');
    }

    public function logout()
    {
        session()->destory();

        redirect('/dep/users/login');
    }
}
