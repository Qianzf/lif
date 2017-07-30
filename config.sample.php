<?php

// --------------------------------------------------------------
//     LiF will not read configurations from this sample file
//     But it indicate how to configure each part in this way
// --------------------------------------------------------------

return [
    'app' => [
        'env'   => 'local',
        'debug' => true,
        'timezone' => 'Asia/Shanghai',
    ],
    'db' => [
        'default' => [
            'host'    => 'db',
            'driver'  => 'mysql',
            'user'    => 'lif',
            'passwd'  => 'lif',
            'dbname'  => 'lif',
            'charset' => 'UTF8',
        ],
    ],
];
