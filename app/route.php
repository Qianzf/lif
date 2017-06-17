<?php

$app->get('/', function () use ($app) {
    $app->jsonResponse(200, 'Hello World', [
        'title'   => 'LiF',
        'version' => 0.01,
    ]);
});

$app->get('user', 'User@get');
$app->post('/user', 'User@create');
