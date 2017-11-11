<?php

namespace Lif\Mdwr\Auth;

class Role
{
    // protected $role = null;

    public function handle($app)
    {
        if ($this->getRole() !== strtolower(share('user.role'))) {
            share_error_i18n('VIEW_PERMISSION_DENIED');
            
            session()->delete('user');

            return redirect('/dep/user/login');
        }
    }

    protected function getRole() : string
    {
        // To get role anme via `$this->role`
        // `$role` of each child class should not be private
        return $this->role ?? strtolower(classname($this));
    }
}
