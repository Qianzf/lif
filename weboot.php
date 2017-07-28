<?php

// -----------------------
//     Web Boot Loader
// -----------------------

require_once __DIR__.'/vendor/autoload.php';

$app = new Lif\Core\App;
$app->handle();
