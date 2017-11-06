<?php

namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\Project as ProjectModel;

class Project extends Ctl
{
    public function index(ProjectModel $project)
    {
        view('ldtdf/project/index')->withProjects($project->all());
    }

    public function add(ProjectModel $project)
    {
        view('ldtdf/project/edit')->withProject($project);
    }

    public function create(ProjectModel $project)
    {
        $id = $project->create($this->request->all());

        if (is_integer($id)) {
            share_error_i18n('CREATED_SUCCESS');
            redirect('/dep/admin/projects/edit/'.$id);
        }

        share_error_i18n('CREATE_FAILED');
        redirect($this->route);
    }

    public function update(ProjectModel $project)
    {
        $status = $project->save($this->request->all());

        if (is_integer($status) && ($status > 0)) {
            share_error_i18n('UPDATED_OK');
        } else {
            share_error(lang('UPDATE_FAILED', $status));
        }

        redirect($this->route);
    }

    public function edit(ProjectModel $project)
    {
        view('ldtdf/project/edit')->withProject($project);
    }
}
