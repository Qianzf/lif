<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    dd(lang('NO_USER'));

    // schema()->table('__dit__')->autoincre(1);
    // schema()->dropDBIfExists('ldtdf');
    // schema()->createDBIfNotExists('ldtdf');

    // db()->truncate('__dit__');die;

    $data = db('sqlite')
    ->select(
        'value as val'
    )
    ->table('task_status')
    ->get();

    // dd(db()->table('task_status')->insert($data));
});
