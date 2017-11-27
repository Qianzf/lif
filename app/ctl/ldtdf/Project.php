<?php

namespace Lif\Ctl\Ldtdf;

use Lif\Core\Ctl as CtlBase;
use Lif\Mdl\Project as ProjectModel;

class Project extends CtlBase
{
    public function info(ProjectModel $project)
    {
        return view('ldtdf/project/info')->withProject($project);
    }
}
