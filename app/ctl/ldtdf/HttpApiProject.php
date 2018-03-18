<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;

use Lif\Mdl\{
    Httpapi,
    HttpapiProject as Project,
    HttpapiCate as Cate
};

class HttpApiProject extends CtlBase
{
    public function editEnv(Project $project)
    {
        return view('ldtdf/tool/httpapi/envs/edit')
           ->withProject($project); 
    }

    public function envs(Project $project)
    {
        return view('ldtdf/tool/httpapi/envs/index')
        ->withProjectEnvs($project, $project->envs());
    }

    public function deleteCate(Project $project, Cate $cate)
    {
        if ($cate->alive()) {
            $cate->delete();
            Httpapi::whereCate($cate->id)->update(['cate' => 0]);
        }

        return redirect(lrn('/tool/httpapi/projects/'.$project->id));
    }

    public function updateCate(Project $project, Cate $cate)
    {
        return $this->responseOnUpdated(
            $cate,
            lrn('/tool/httpapi/projects/'.$project->id)
        );
    }

    public function createCate(Project $project, Cate $cate)
    {
        return $this->responseOnCreated(
            $cate,
            lrn('/tool/httpapi/projects/'.$project->id)
        );
    }

    public function info(Project $project)
    {
        if (! $project->alive()) {
            share_error_i18n('NO_PROJECT');
            return redirect(lrn('/tool/httpapi'));
        }
        
        $cates = $project->cates();

        return view(
            'ldtdf/tool/httpapi/projects/info'
        )
        ->withCates($cates)
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

    public function update(Project $project)
    {
        return $this->responseOnUpdated($project, lrn('tool/httpapi/projects/'.$project->id));
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
