Route register examples:

- Basic

``` php
// CASE-1
$this->get('/', function () {
    lif();
});

// CASE-2
$this->get([
    'name' => '/test',
    'as'   => 't.e.s.t',
    'prefix' => 'demo',
    'middleware' => [
        'auth.test',
    ],
    'bind' => 'Test@test',
    // 'bind' => function () {
    //     // do sth
    // }
]);
```

#### NOTICE

In `CASE-2`, if the value of `bind` key is an anonymous function or a closure, then the codes in it will be executed twice.

- Nested Group

``` php
$this->group([
    'prefix' => 'test',
], function () {
    $this->group([
        'prefix' => 'demo',
    ], function () {
        $this->get('/', 'Demo@index');
        $this->get('a', 'Demo@a');
        $this->get('b', 'Demo@b');
    });

    $this->get('/', 'Test@index');
    $this->get('a', 'Test@a');
    $this->get('b', 'Test@b');
});
```

- Any

``` php
$this->any('/sys_msg', function () {
    response((new \Lif\Core\SysMsg)->get());
});
```

- Match

``` php
$this->match([
    'get',
    'post',
    'put',
], 'passwd', function () {
    abort(403, 'Forbidden');
});
```

- Middlewares, alias, route params

``` php
$this->get('user/{id}', [
    'middleware' => [
        auth.jwt',
    ],
    'prefix' => 'lif',
    'alias' => 'user_info',
], 'User@query');

$this->group([
    'middleware' => [
        'auth.web'
    ],
], function () {
    // Cancel middleware for specific route
    $this->get('/', 'User@info');
    $this->get('login', 'Passport@login')->cancel('auth.web');
    $this->post('login', 'Passport@loginAction')->cancel('auth.web');
});
```

#### NOTICE

`alias` is mainly used for getting its raw route:

``` php
$alias = 'user_login';
$route = route($alias);
redirect($route);
```

__`alias` has a default value if no `alias` explicit indicated in that route.__

And naming style like this: for route `a/b/c`, its alias will be `a.b.c`.

- Variables assignment

``` php
$this->group([
    'prefix' => 'a/{b}',    // On route prefix
], function () {
    // On route name
    $this->get('c/{d}', function () {
        dd($this->vars);
    });
});
```

- Route parameters filter

``` php
$this
->get('{id}', function (\Lif\Mdl\User $user) {
    dd($user->email);
})
->filter([
    'id' => 'int|min:1',
]);

$this->group([
    'filter' => [
        'id' => 'int|min:1',
    ],
], function () {
    $this->get('{id}', 'User@detail');
});
```

- Specific controller

``` php
$this->group([
    'prefix' => 'users',
    'ctl' => 'User',
], function () {
    $this->get('/', 'index');
    $this->get('new', 'edit');
    $this->post('new', 'add');
});
```
