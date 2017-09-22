#### Configure Application

``` php
<?php

return [
    'env'   => 'local',
    'debug' => true,
    'timezone' => 'Asia/Shanghai',
    'view'  => [
        'cache' => false
    ],
    'route' => [
        // 'cache' => true,
    ]
];
```

#### Configure Database

``` php
<?php

return [
    'default' => 'local_sqlite',

    'conns' => [
        'local_sqlite' => [
            'driver' => 'sqlite',
            'memory' => false,
            'path' => '/var/db/db.sqlite',
        ],
        'hcm' => [
            'driver' => 'mysql',
            'host' => 'db',
            'user' => 'root',
            'passwd' => '123456',
            'dbname' => 'lif',
        ],
    ],
];
```

#### Custom configuration files

Syntax: `conf($filename)`.

For example: `conf('custom')` means read configurations from app/conf/custom.php.

#### Read configurations

- All: `conf_all()`

- Single one of specific part: `config('app.route.cache')` means get this application's route cache configuration.
