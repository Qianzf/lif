<?php

// -----------------------------------
//     Web Application Boot Loader
// -----------------------------------

require_once __DIR__.'/../../vendor/autoload.php';

(
    new Lif\Core\App
)

// -------------------------------------------------------------
//     Tell web strategy how many global middlewares we need
// -------------------------------------------------------------

->withMiddlewares(
    'cors'
)

// -------------------------------------------------------------
//     Tell web strategy how many route files need to add in
// -------------------------------------------------------------

->withRoutes(
    'api',
    'ldtdf'
);
