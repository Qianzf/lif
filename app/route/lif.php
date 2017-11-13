<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    $schema = new \Lif\Core\Storage\SQL\Schema;

    $res = $schema
    ->table('user')
    ->addCol('group')
    // ->first()
    ->after('role')
    ->tinyint();
    // ->default(1)
    // ->comment('User group ID');

    dd($res);
});
