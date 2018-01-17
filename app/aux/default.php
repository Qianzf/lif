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
if (! fe('update_tasks_when_update_origin')) {
    function update_tasks_when_update_origin($user, $origin, $developers) {
        $principals = ($before = (array) $origin->getPrincipals())
        ? array_column($before, 'id') : [];

        $toCancel = $toCreate = [];

        foreach ($developers as $developer) {
            if (! in_array($developer, $principals)) {
                $toCreate[] = [
                    'origin_type' => $origin->getTaskOriginName(),
                    'origin_id'   => $origin->id,
                    'creator'     => $user,
                    'first_dev'   => $developer,
                    'last'        => $user,
                    'current'     => $developer,
                    'status'      => 'waiting_edit',
                    'create_at'   => fndate(),
                ];
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

        $origin->addTrending('update', $user);
    }
}
