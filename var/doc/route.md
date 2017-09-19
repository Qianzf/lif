Route register examples:

- Basic

``` php
$this->get('/', function () {
    lif();
});
```

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
```

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
