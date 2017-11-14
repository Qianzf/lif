<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    
    schema()->createDBIfNotExists('tes2t', function ($db) {
        $db->charset('utf8')->collate('utf8_unicode_ci');
    });

    // schema()->dropIfExists('test', 'test2');

    // schema()->dropDBIfExists('test', true);

    // schema()->table('user')->rename('user_tmp');

    // schema()->table('trending')->rename('trending_tmp');

    // db('local_sqlite')->table('environment')->get();

    // db()->table('user')->whereId('>', 0)->update('group', 0);
});
