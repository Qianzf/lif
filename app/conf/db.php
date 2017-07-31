<?php

return [
    'default' => 'mysql_master_rw_0',
    'conns' => [
        'mysql_master_rw_0' => [
            'host'    => 'db',
            'driver'  => 'mysql',
            'user'    => 'root',
            'passwd'  => '123456',
            'dbname'  => 'lif',    // optional
            'charset' => 'UTF8',
        ],
        'conn_2' => [
            'host'    => 'db',
            'driver'  => 'mysql',
            'user'    => 'root',
            'passwd'  => '123456',
            'dbname'  => 'hcm',    // optional
            'charset' => 'UTF8',
        ],
    ],
];
