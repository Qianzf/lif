<?php

namespace Lif\Mdwr\Auth;

class Role extends \Lif\Core\Abst\Middleware
{
    // protected $role = null;

    public function passing($app)
    {
        if (($this->getRole() !== strtolower(share('user.role')))
            && (! model(\Lif\Mdl\User::class, share('user.id'))
                ->hasPermission($app->request->type, $app->request->route)
            )
        ) {
            share_error_i18n('VIEW_PERMISSION_DENIED');
            
            session()->delete('user');

            return redirect(lrn('users/login'));
        }
    }

    protected function getRole() : string
    {
        // To get role anme via `$this->role`
        // `$role` of each child class should not be private
        return $this->role ?? strtolower(classname($this));
    }
}
