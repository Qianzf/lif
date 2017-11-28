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
        $id = $project->create($this->request->posts());

        if (is_integer($id)) {
            share_error_i18n('CREATED_SUCCESS');
            redirect('/dep/admin/projects/'.$id);
        }

        share_error_i18n('CREATE_FAILED');
        redirect($this->route);
    }

    public function update(ProjectModel $project)
    {
        $status = $project->save($this->request->posts());

        if (is_integer($status) && ($status >= 0)) {
            share_error_i18n('UPDATED_OK');
        } elseif (is_string($status)) {
            share_error(lang('UPDATE_FAILED', $status));
        }

        return redirect($this->route);
    }

    public function add(ProjectModel $project)
    {
        share('hide-search-bar', true);
        
        view('ldtdf/admin/project/edit')->withProject($project);
    }

    public function edit(ProjectModel $project)
    {
        $error = $back2last = null;
        if (! $project->isAlive()) {
            $error     = lang('NO_PROJECT');
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
