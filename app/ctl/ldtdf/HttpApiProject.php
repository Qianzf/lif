<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;

use Lif\Mdl\HttpapiProject as Project;

class HttpApiProject extends CtlBase
{
    public function info(Project $project)
    {
        return view(
            'ldtdf/tool/httpapi/projects/info'
        )
        ->withProject($project);
    }

    public function edit(Project $project)
    {
        return view(
            'ldtdf/tool/httpapi/projects/edit'
        )
        ->withProject($project)
        ->share('hide-search-bar', true);
    }

    public function create(Project $project)
    {
        $this->request->setPost('creator', share('user.id'));
        $this->request->setPost('create_at', fndate());

        return $this->responseOnCreated(
            $project,
            lrn('tool/httpapi/projects/?')
        );   
    }
}
