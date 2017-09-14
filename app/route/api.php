<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', function () {
    lif();
});

$this->group([
    'prefix' => 'dep',
], function () {
    $this->get('/', 'LDTDF@index');
    $this->get('profile', 'LDTDF@profile');
    $this->get('logout', 'LDTDF@logout');
});

$this->any('/sys_msg', function () {
    response((new \Lif\Core\SysMsg)->get());
});

$this->get('user/{id}', [
    'middleware' => [
        'auth.jwt',
    ],
    // 'prefix' => 'lif',
    'alias' => 'get_user',
], 'API@user');

$this->any('test', [
    'middleware' => [
        'auth',
    ],
    // 'alias' => 'test',
], function () {
    lif();
});

$this->match([
    'get',
    'post',
    'put',
], 'passwd', [
    'middleware' => [
        'auth.jwt'
    ],
], function () {
    abort(403, 'Forbidden');
});
