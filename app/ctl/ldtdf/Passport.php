<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Web\Session;
use Lif\Mdl\User;

class Passport extends Ctl
{
    public function login(Session $s)
    {
        if ($s->get('LOGGED_USER')) {
            redirect(route('dep'));
        }

        view('ldtdf/user/login');
    }

    public function loginAction(Session $s, User $u)
    {
        $lang    = $this->request->get('lang');
        $account = $this->request->get('account');
        $passwd  = $this->request->get('passwd');
        $user    = $u->select('account', 'email', 'passwd')
        ->whereAccount($account)
        ->orEmail($account)
        ->first();

        if (! $user) {
            share('__error', sysmsg('NO_USER'));
            redirect(route('dep.user.login'));
        } elseif (! password_verify($passwd, $user['passwd'])) {
            share('__error', sysmsg('ILLEGAL_USER_CREDENTIALS'));
            redirect(route('dep.user.login'));
        }

        unset($user['passwd']);

        $s->set('LOGGED_USER', $user);
        
        if ($lang) {
            $s->set('__lang', $lang);
        }

        share('nameWitchRole', $user['account']);

        redirect(route('dep'));
    }

    public function logout(Session $s)
    {
        $s->delete('LOGGED_USER');

        redirect(route('dep.user.login'));
    }
}
