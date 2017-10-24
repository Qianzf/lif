#### Queue config

``` php
return [
    'type'  => 'db',
    'conn'  => 'local_sqlite',
    'table' => 'job',
    'defs'  => [
        // 'id',
        // 'queue',
        // 'detail',
        // 'tried',
        // 'create_at',
        // 'finish_at',
        // 'restart',
        // 'retry',
    ],
];    
```

If `defs` is unset or empty, then will use `default_queue_defs_get()` instead.
