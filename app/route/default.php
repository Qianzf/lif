<?php

$app->get('/', function () {
    response([
        'title'   => 'LiF',
        'version' => '0.0.0',
    ], 'Hello World');
});
