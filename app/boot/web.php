<?php

// -----------------------------------
//     Web Application Boot Loader
// -----------------------------------

require_once __DIR__.'/../../vendor/autoload.php';

(
    new Lif\Core\App
)

// -------------------------------------------
//     How many global middlewares we need
// -------------------------------------------

->withMiddlewares(
    'cors',
    'safty.csrf'
)

// ------------------------------------
//     How many route files we need
// ------------------------------------

->withRoutes(
    'lif',
    'ldtdf'
)

;
