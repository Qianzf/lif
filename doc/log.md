## Config logger

``` php
// conf/log.php
return [
    'default' => 'file_log',

    'loggers' => [
        'file_log' => [
            'driver' => 'file',
            // 'path' => 'lif.log',
        ],
    ],
];
```

## Use logger

``` php
// Get default logger instance from conf/log.php
$logger = logger();

// Get specific logger instance from conf/log.php
$logger = logger('db_log');    // `db` => key of array `loggers`

// Get dynamic logger instance from given configs
$logger = logger([
    'driver' => 'file',    // Required supported `driver` key
    'path'   => 'lif.log',
]);

// Call method of given logger
logger('file_log')->setPath('test')->alert('help');

// Logging using $logger object
$logger->info('User {name} logged in.', [
    'name' => 'cjli',
]);

// Or, logging using `logging()` helper
logging('User {name} logged in.', [
    'name' => 'cjli',
    'info',
    // `$logger` here can be a: 
    // => string : key of array `loggers`
    // => array : logger configs
    // => object : logger instance
    $logger
]);
```
