<?php

$this->any('/sys_msg', function () {
    response(sysmsgs());
});

$this->group([
    'prefix'    => 'dep',
    'namespace' => 'Ldtdf',
    'filter' => [
        'id' => 'int|min:1',
    ],
    'middleware' => [
        'auth.web',
    ],
], function () {
    $this->get('/', 'LDTDF@index');
    $this->get('trending', 'User@trending');
    $this->get('todo', 'User@todo');

    $this->group([
        'prefix' => 'tool',
    ], function () {
        $this->get('/', 'Tool@index');

        $this->group([
            'prefix' => 'uploads',
            'ctl' => 'Upload',
        ], function () {
            $this->get('/', 'list');
            $this->get('new', 'upload');
            $this->post('new', 'add');
            $this->get('{id}', 'edit');
            $this->post('{id}', 'update');
            $this->get('uptoken', [
                'middleware' => 'auth.qiniu',
            ], 'uptoken');
        });
    });

    $this->group([
        'prefix' => 'users',
    ], function () {
        $this->get('login', 'Passport@login')->cancel('auth.web');
        $this->post('login', 'Passport@auth')->cancel('auth.web');
        $this->get('logout', 'Passport@logout');
        $this->get('{id}', 'User@info');
        $this->get('list', 'User@list');
        $this->get('profile', 'User@profile');
        $this->post('profile', 'User@update');
    });

    $this->group([
        'prefix' => 'stories',
        'ctl' => 'Story',
    ], function () {
        $this->get('/', 'index');
        $this->get('list', 'list');
        $this->get('new', 'add');
        $this->post('new', 'create');
        $this->get('{id}', 'info');
        $this->get('{id}/edit', 'edit');
        $this->post('{id}', 'update');
    });

    $this->group([
        'prefix' => 'docs',
        'ctl' => 'Doc',
    ], function () {
        $this->get('/', 'index');
        $this->get('folders', 'index');
        $this->get('my', 'my');
        $this->get('new', 'edit');
        $this->get('{id}', 'viewDoc');
        $this->get('{id}/edit', 'edit');
        $this->post('{id}/edit', 'update');
        $this->post('new', 'create');
        $this->get('folders/children', 'getChildren');
        $this->get('folders/{id}', 'viewFolder');
        $this->get('folders/{id}/edit', 'editFolder');
        $this->get('folders/new', 'editFolder');
        $this->get('folders/{id}/unfold', 'queryFolderChildren');
        $this->post('folders/new', 'createFolder');
        $this->post('folders/{id}/edit', 'updateFolder');
    });

    $this->group([
        'prefix' => 'bugs',
        'ctl' => 'Bug',
    ], function () {
        $this->get('/', 'index');
        $this->get('new', 'edit');
        $this->post('new', 'create');
        $this->get('{id}', 'info');
        $this->get('{id}/edit', 'edit');
        $this->post('{id}', 'update');
    });

    $this->group([
        'prefix' => 'tasks',
        'ctl' => 'Task',
    ], function () {
        $this->get('/', 'index');
        $this->get('new', 'add');
        $this->post('new', 'create');
        $this->get('{id}', 'info');
        $this->post('{id}', 'update');
        $this->get('{id}/edit', 'edit');
        $this->get('{id}/assign', 'assign');
        $this->post('{id}/assign', 'assignTo');
        $this->post('{id}/confirm', 'confirm');
        $this->post('{id}/cancel', 'cancel');
        $this->post('{id}/activate', 'activate');
        $this->get('{id}/users/assignable', 'getAssignableUsers');
        $this->get('stories/attachable', 'getAttachableStories');
        $this->get('bugs/attachable', 'getAttachableBugs');
    });

    $this->group([
        'prefix'    => 'projects',
        'ctl'       => 'Project',
    ], function () {
        $this->get('{id}', 'info');
    });

    $this->group([
        'prefix' => 'dev',
        'ctl' => 'Developer',
        'middleware' => [
            'auth.developer',
        ],
    ], function () {
        $this->get('/', 'index');
    });

    $this->group([
        'prefix' => 'test',
        'namespace' => 'Tester',
        'ctl' => 'Tester',
        'middleware' => [
            'auth.tester',
        ],
    ], function () {
        $this->get('/', 'index');

        $this->group([
            'prefix' => 'regressions',
            'filter' => [
                'env'  => 'int|min:1',
                'task' => 'int|min:1',
            ],
            'ctl' => 'Regression',
        ], function () {
            $this->get('/', 'index');
            $this->get('{id}', 'relateTasks');
            $this->get('{id}/start', 'startTest');
            $this->get('{env}/unpass', 'setUnpass');
            $this->get('{env}/{task}/unpass', 'setTaskUnpass');
        });
    });

    $this->group([
        'prefix' => 'ops',
        'ctl' => 'Operator',
        'middleware' => [
            'auth.operator',
        ],
    ], function () {
        $this->get('/', 'index');
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
            
            $this->group([
                'prefix' => 'groups',
                'ctl' => 'UserGroup',
            ], function () {
                $this->get('/', 'index');
                $this->get('new', 'add');
                $this->post('new', 'create');
                $this->get('{id}', 'edit');
                $this->post('{id}', 'update');
            });
        });

        $this->group([
            'prefix' => 'projects',
            'ctl'    => 'Project',
        ], function () {
            $this->get('/', 'index');
            $this->get('new', 'add');
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
});
