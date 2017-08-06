<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', function () {
    lif();
});

$this->get('user/{id}', [
    'middleware' => [
        // 'auth',
    ],
    // 'prefix' => 'lif',
    'alias' => 'get_user',
], 'API@user');

$this->any('test', [
    'middleware' => [
        'auth'
    ],
    // 'alias' => 'test',
], function () {
    lif();
});

$this->match([
    'gET',
    'POST',
    'PUT',
], 'passwd', [
    'middleware' => [
        // 'auth'
    ],
], function () {
    abort(403, 'Forbidden');
});
