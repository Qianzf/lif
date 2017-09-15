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
        // 'auth.jwt',
    ],
], function () {
    $this->get('/', 'LDTDF@index');
    $this->get('profile', 'LDTDF@profile');
    $this->get('logout', 'LDTDF@logout');
});
