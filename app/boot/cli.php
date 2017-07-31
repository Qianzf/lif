<?php

// ------------------------------------
//     Command Line Interface Entry
// ------------------------------------

require_once __DIR__.'/../../vendor/autoload.php';

(
    new Lif\Core\App
)

// --------------------------------------------------------
//     Pass arguments from command line to cli strategy
// --------------------------------------------------------

->setArgvs($argv)

// ----------------------------
//     Execute cli strategy
// ----------------------------

->fire();
