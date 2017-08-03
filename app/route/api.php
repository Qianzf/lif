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
