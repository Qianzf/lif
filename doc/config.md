## Explanation

See all details in _doc/con-all-sample.md_.

## Custom configuration files

Syntax: `conf($filename)`.

For example: `conf('custom')` means read configurations from _app/conf/custom.php_.

## Read configurations

- All: `conf_all()`

- Single one of specific part: `config('app.route.cache')` means get this application's route cache configuration.

## Configuration samples

#### Configure Application

``` php
<?php
// app/conf/app.php

return [
    'env'   => 'local',
    'debug' => true,
    'timezone' => 'Asia/Shanghai',    // !!! Default is UTC
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
// app/conf/db.php

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

#### Configure Queue

``` php
<?php
// app/conf/queue.php

return [
    'default' => 'sqlite_queue',

    'conns' => [
        'sqlite_queue' => [
            'type'  => 'db',
            'conn'  => 'local_sqlite',
            'table' => 'queue_job',
            'defs'  => [
                // ...
            ],
        ],
    ],
];
```

#### Configure Log

``` php
// app/conf/log.php

return [
    'default' => 'file_log',

    'loggers' => [
        'file_log' => [
            'driver' => 'file',
            'path' => 'lif.log',
        ],
    ],
];

```

#### Configure Mail

``` php
<?php
// app/conf/mail.php

return [
    'default' => 'swiftmailer',

    'senders' => [
        'swiftmailer' => [
            'driver'  => 'swift-omailer',
            'host'  => 'smtp.example.com',
            'port'  => 465,
            'account' => 'user@example.com',
            'credential' => '*******',
            'sender_name' => 'User',
            'sender_email' => 'user@example.com',
            'encryption' => 'ssl',
        ],
    ],
];
```
