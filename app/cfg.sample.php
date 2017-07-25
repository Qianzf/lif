<?php

// Copy this file and save as cfg.php and ignore cfg.php in version control

return [
	'env'   => 'local',
	'route' => [
		'api_prefix' => '/',
	],
    'pdo' => [
		'dsn'    => 'mysql:dbname=lif;charset=UTF8;host=db',
		'user'   => 'root',
		'passwd' => '123456',
	],
    'custom' => [
    ],
];
