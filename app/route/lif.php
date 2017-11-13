<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    // create environment table

    db('local_sqlite')->table('environment')->get();

    $schema->dropTableIfExists('demo', 'test');

    // $schema
    // ->table('user')
    // ->modifyColumn('group')
    // ->after('status')
    // ->tinyint()
    // ->default(0);

    // db()->table('user')->whereId('>', 0)->update('group', 0);
});
