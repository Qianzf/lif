<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

// $this->get('/', 'lif');

$this->get('__test__', function () {
    // enqueue(new \Lif\Job\Job)->timeout(300)->on('t1');
    // enqueue(new \Lif\Job\Job)->timeout(600)->on('t2');
});
