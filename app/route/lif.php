<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    // $dit = new Lif\Dat\Dbvc\CreateUserTable;
    // $dit = new Lif\Dat\Dbvc\CreateJobTable;

    // $dit->revert();
    // $dit->commit();
});
