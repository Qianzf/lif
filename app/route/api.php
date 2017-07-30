<?php

// -------------------------------------------------------
//     This is default route file of LiF
//     3 variables can be used to register route:
//     - `$this` => route groups needn't `use ($this)`
//     - `$app`  => route groups need `use ($app)`
//     - `$web`  => route groups need `use ($web)`
// -------------------------------------------------------

$this->get('/', function () {
    lif();
});

$this->get('user', 'API@user');
