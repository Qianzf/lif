<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', function () {
    lif();
});

$this->any('/sys_msg', function () {
    response(sysmsgs());
});

$this->group([
    'prefix'    => 'dep',
    'namespace' => 'Ldtdf',
    'middleware' => [
        'auth.web',
    ],
], function () {
    $this->get('/', 'LDTDF@index');

    $this->group([
        'prefix'    => 'admin',
        'namespace' => 'Admin',
        'middleware' => [
            'auth.admin',
        ],
    ], function () {
        $this->get('/', 'Admin@index');
    });

    $this->group([
        'prefix' => 'user',
    ], function () {
        $this->get('login', 'Passport@login')->cancel('auth.web');
        $this->post('login', 'Passport@loginAction')->cancel('auth.web');
        $this->get('profile', 'User@profile');
        $this->post('profile', 'User@update');
        $this->get('logout', 'Passport@logout');
    });
});
