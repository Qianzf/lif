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
    response((new \Lif\Core\SysMsg)->get());
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
        'prefix' => 'user',
    ], function () {
        $this->get('{id}', 'User@profile');
        $this->get('login', 'Passport@login')->cancel('auth.web');
        $this->post('login', 'Passport@loginAction')->cancel('auth.web');
        $this->get('logout', 'Passport@logout');
    });
});
