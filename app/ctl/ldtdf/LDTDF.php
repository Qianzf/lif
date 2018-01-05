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
        if (($payload = $this->request->posts())
            && (
                ($event = ($payload['event_name'] ?? false))
                && ('push' == strtolower($event))
            )
            && ($url = ($payload['project']['url'] ?? false))
            && ($branch = ($payload['ref'] ?? false))
        ) {
            enqueue(
                (new \Lif\Job\UpdateTaskEnv)
                ->setOrigin('gitlab')
                ->setUrl($url)
                ->setBranch($branch)
                ->setToken(server('HTTP_X_GITLAB_TOKEN'))
            )
            ->on('update_task_env')
            ->try(3)
            ->timeout(600);    // 10 minutes
        }
    }
}
