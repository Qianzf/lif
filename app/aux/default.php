<?php

// lrn('admin/userssss')

// -------------------------------------
//     User defined Helper Functions
// -------------------------------------

if (! fe('ldtdf')) {
    function ldtdf(string $subtitlekey = null) {
        $sitename = ($appname = config('app.name'))
        ? $appname.L('TFMS')
        : L('LDTDFMS');

        $subtitle = $subtitlekey ? L($subtitlekey).' - ' : '';

        return $subtitle.$sitename;
    }
}
if (! fe('get_ldtdf_devs')) {
    function get_ldtdf_devs() {
        return db()
        ->table('user')
        ->select('id', 'name', 'ability', 'role')
        ->whereStatusRole(1, 'dev')
        ->get();
    }
}
if (! fe('get_ldtdf_products')) {
    function get_ldtdf_products() {
        return db()
        ->table('product')
        ->select('id', 'name')
        ->get();
    }
}
if (! fe('prepare_task_when_manipulate_origin')) {
    function prepare_task_when_manipulate_origin($origin, $developer) {
        return [
            'origin_type' => $origin->getTaskOriginName(),
            'origin_id'   => $origin->id,
            'creator'     => $origin->creator,
            'first_dev'   => $developer,
            'last'        => $origin->creator,
            'current'     => $developer,
            'status'      => 'waiting_edit',
            'create_at'   => fndate(),
        ];
    }
}
if (! fe('create_tasks_when_create_origin')) {
    function create_tasks_when_create_origin($origin, $developers) {
        if ($developers && is_array($developers)) {
            // Create task with out project here
            $tasks = [];
            foreach ($developers as $developer) {
                if (! ispint($developer, false)) {
                    share_error_i18n('ILLEGAL_DEVELOPER');

                    return db()->rollback();
                }

                $tasks[] = prepare_task_when_manipulate_origin(
                    $origin, $developer
                );
            }
            if (! $origin->createTasks($tasks)) {
                return db()->rollback();
            }
        }

        $origin->addTrending('create', $origin->creator);

        return true;
    }
}
if (! fe('update_tasks_when_update_origin')) {
    function update_tasks_when_update_origin($origin, $developers) {
        $principals = ($before = (array) $origin->getPrincipals())
        ? array_column($before, 'id') : [];

        $toCancel = $toCreate = [];

        foreach ($developers as $developer) {
            if (! in_array($developer, $principals)) {
                $toCreate[] = prepare_task_when_manipulate_origin(
                    $origin, $developer
                );
            }
        }

        foreach ($principals as $principal) {
            if (! in_array($principal, $developers)) {
                $toCancel[] = $principal;
            }
        }

        if (false
            || ($toCancel && !$origin->cancelRelateTasks($toCancel))
            || ($toCreate && !$origin->createTasks($toCreate)
            || !$origin->tryActivateRelatedTasks($developers)
            )
        ) {
            return db()->rollback();
        }

        share_error_i18n('UPDATE_OK');

        $origin->addTrending('update', $origin->creator);

        return true;
    }
}
