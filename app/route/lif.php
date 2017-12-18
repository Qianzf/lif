<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    // $status = db()
    // ->table('task_status')
    // ->select(function () {
    //     return 'UPPER(`key`) AS `status`';
    // })
    // ->where('assignable', 'yes')
    // ->get();

    // foreach ($status as $s) {
    //     $echo = $s['status'].' => '.L('ASSIGN_'.$s['status']);
    //     // pr(L('STATUS_'.$s['status']));
    //     pr($echo);
    // }
});
