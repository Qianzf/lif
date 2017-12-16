<?php

namespace Lif\Ctl\Ldtdf;

use \Lif\Mdl\Task;

class LDTDF extends Ctl
{
    public function index()
    {
        $entryRouteOfRole = '/dep/'.strtolower(share('user.role'));

        redirect($entryRouteOfRole);
    }

    // 1. Check secure token
    // 2. Parse out payload
    // 3. Findout task by related project url
    // 4. Update task env when status is correct
    public function gitlabWebhook(Task $task)
    {
    }
}
