## Config SSH Server

``` php
// app/conf/ssh.php

return [
    'default' => 'example',

    'servers' => [
        'example' => [
            'host' => 'example.com',
            // 'port' => 22,
            // 'auth' => 'pki',
            // 'user' => 'root',
            // 'pswd' => 'lif',
            'pubk'  => '/home/www/.ssh/id_rsa.pub',
            'prik'  => '/home/www/.ssh/id_rsa',
        ],
    ],
];
```

## Connect and execute shell commands

- Without other extension

``` php
dd(ssh_exec([
    'cd /data/wwwroot/example.com',
    'composer install --no-dev --optimize-autoloader',
    'php artisan migrate',
]));
```

- Use ssh2 extension

``` php
$ssh2 = new \Lif\Core\Lib\Connect\SSH2('example.com');

$ssh2 = $ssh2
->setPubkey('/home/www/.ssh/id_rsa.pub')
->setPrikey('/home/www/.ssh/id_rsa')
->connect([
    'hostkey' => 'ssh-rsa',
]);

dd($ssh2->exec([
    'env'
]));
```
