<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;

use Lif\Mdl\{
    User, HttpapiProject as Project
};

class HttpApi extends CtlBase
{
    public function index(User $user, Project $project)
    {
        $querys = $this->request->gets();

        legal_or($querys, [
            'search'  => ['string', null],
            'creator' => ['int|min:1', null],
            'sort'    => ['ciin:desc,asc', 'desc'],
        ]);

        $users = $user->list(['id', 'name'], null, false);

        return view(
            'ldtdf/tool/httpapi/index'
        )
        ->withUsersProjects(
            array_combine(
                array_column($users, 'id'),
                array_column($users, 'name')
            ),

            $project->list($querys)
        );
    }
}
