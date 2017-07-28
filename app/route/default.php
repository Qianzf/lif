<?php

$app->get('/', function () {
    $lif = lif();
    response([
        'Name'    => $lif->name,
        'version' => $lif->version,
    ], 'Hello World');
});
