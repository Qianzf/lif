<?php

namespace Lif\Ctl\Ldtdf;

class LDTDF extends Ctl
{
    public function index()
    {
        $entryRouteOfRole = '/dep/'.strtolower(share('user.role'));

        redirect($entryRouteOfRole);
    }

    public function gitlabWebhook(Task $task)
    {
        // Parse out params in request payload and header
        //  - hook type
        //  - project url
        //  - branch name
        //  - token
        if (($payload = json_decode(file_get_contents('php://input')))
            && (
                ($event = ($payload->event_name ?? false))
                && ('push' == strtolower($event))
            )
            && ($url = ($payload->project->url ?? false))
            && ($branch = ($payload->ref ?? false))
        ) {
            enqueue(
                (new \Lif\Job\UpdateTaskBranch)
                ->setUrl($url)
                ->setBranch($branch)
                ->setToken(server('HTTP_X_GITLAB_TOKEN'))
            )
            ->on('update_task_branch')
            ->try(3)
            ->timeout(5);
        }
    }
}
