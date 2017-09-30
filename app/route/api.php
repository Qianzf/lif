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
    $this->get('trending', 'User@trending');

    $this->group([
        'prefix' => 'tasks',
        'filter' => [
            'id' => 'int|min:1',
        ],
    ], function () {
        $this->get('/', 'Task@index');
        $this->get('{id}', 'Task@detail');
        $this->post('{id}', 'Task@update');
        $this->post('edit', 'Task@edit');
    });

    $this->group([
        'prefix'    => 'admin',
        'namespace' => 'Admin',
        'middleware' => [
            'auth.admin',
        ],
    ], function () {
        $this->get('/', 'Admin@index');
        $this->group([
            'prefix' => 'users',
        ], function () {
            $this->get('/', 'User@index');
            $this->get('new', 'User@info');
            $this->post('new', 'User@add');
            $this->get('edit/{id}', 'User@info');
            $this->get('delete/{id}', 'User@delete');
            $this->post('edit/{id}', 'User@update');
        });
    });

    $this->group([
        'prefix'    => 'developer',
        'namespace' => 'Developer',
        'middleware' => [
            'auth.developer',
        ],
    ], function () {
        $this->get('/', 'Developer@index');
    });

    $this->group([
        'prefix'    => 'tester',
        'namespace' => 'Tester',
        'middleware' => [
            'auth.tester',
        ],
    ], function () {
        $this->get('/', 'Tester@index');
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
