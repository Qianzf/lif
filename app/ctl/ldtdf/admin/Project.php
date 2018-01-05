<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Project as ProjectModel;

class Project extends Ctl
{
    public function index(ProjectModel $project)
    {
        view('ldtdf/admin/project/index')->withProjectsRecords(
            $project->all(),
            $project->count()
        );
    }

    public function create(ProjectModel $project)
    {
        return $this->responseOnCreated($project, lrn('admin/projects/?'));
    }

    public function update(ProjectModel $project)
    {
        return $this->responseOnUpdated($project);
    }

    public function add(ProjectModel $project)
    {
        share('hide-search-bar', true);
        
        view('ldtdf/admin/project/edit')->withProject($project);
    }

    public function edit(ProjectModel $project)
    {
        $error = $back2last = null;
        if (! $project->alive()) {
            $error     = L('NO_PROJECT');
            $back2last = share('url_previous');
        }

        shares([
            'hide-search-bar' => true,
            '__error'   => $error,
            'back2last' => $back2last,
        ]);

        view('ldtdf/admin/project/edit')->withProject($project);
    }
}
