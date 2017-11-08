<?php

$this->group([
    'prefix'    => 'dep',
    'namespace' => 'Ldtdf',
    'filter' => [
        'id' => 'int|min:1'
    ],
    'middleware' => [
        'auth.web',
    ],
], function () {
    $this->get('/', 'LDTDF@index');
    $this->get('trending', 'User@trending');

    $this->group([
        'prefix' => 'user',
    ], function () {
        $this->get('login', 'Passport@login')->cancel('auth.web');
        $this->post('login', 'Passport@auth')->cancel('auth.web');
        $this->get('logout', 'Passport@logout');
        $this->get('profile', 'User@profile');
        $this->post('profile', 'User@update');
    });

    $this->group([
        'prefix' => 'tasks',
        'ctl' => 'Task',
    ], function () {
        $this->get('/', 'index');
        $this->get('new', 'edit');
        $this->post('new', 'create');
        $this->get('{id}', 'edit');
        $this->post('{id}', 'update');
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
            'ctl' => 'User',
        ], function () {
            $this->get('/', 'index');
            $this->get('new', 'info');
            $this->post('new', 'add');
            $this->get('{id}', 'info');
            $this->post('{id}', 'update');
            $this->get('delete/{id}', 'delete');
        });

        $this->group([
            'prefix' => 'projects',
            'ctl'    => 'Project',
        ], function () {
            $this->get('/', 'index');
            $this->get('new', 'edit');
            $this->post('new', 'create');
            $this->get('{id}', 'edit');
            $this->post('{id}', 'update');
            $this->get('delete/{id}', 'delete');
        });

        $this->group([
            'prefix' => 'envs',
            'ctl' => 'Environment',
        ], function () {
            $this->get('/', 'index');
            $this->get('new', 'edit');
            $this->post('new', 'create');
            $this->get('{id}', 'edit');
            $this->post('{id}', 'update');
        });

        $this->group([
            'prefix' => 'servers',
            'ctl' => 'Server',
        ], function () {
            $this->get('/', 'index');
            $this->get('new', 'edit');
            $this->post('new', 'create');
            $this->get('{id}', 'edit');
            $this->post('{id}', 'update');
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
});
