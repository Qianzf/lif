<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    $task = db()
    ->table('task', 't')
    ->leftJoin(['project', 'p'], 't.project', 'p.id')
    ->select('t.id', 't.env')
    ->where('t.env', '>', 0)
    ->where([
        't.branch' => 1,
        'p.url'    => 1,
    ])
    ->get(false, 2);

    ee($task);
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
