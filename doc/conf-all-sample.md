``` php
// ---------------------------------------------------------------------
//     Indicates how to configure each part in the way below
//     
//     Every first level KEY of this array is one single part
//     which can be configured in a standalone file in `/app/conf/`
//     For exampe:
//     `/app/conf/app.php` is mapping to this array's subarray `app`
//     and should return the value of this subarray (only)
//     
//     Each configure with default value means it's not mandatory
//     Otherwise, it is an requirement if you want that configuring
//     part works correctly
// ---------------------------------------------------------------------

return [

    // --------------------------------------------------
    //     `app` => Application's meta configurations
    // --------------------------------------------------
    'app' => [

        // ------------------------------------------
        //     `env` => Application's environment
        //     - production
        //     - local   <= default
        // ------------------------------------------
        'env'   => 'local',

        // ------------------------------
        //     `debug` => Debug model
        //     - true    <= default
        //     - false
        // ------------------------------
        'debug' => true,

        // ------------------------------------------------
        //     `timezone` => System timezone
        //     default => UTC, see list:
        //     <http://php.net/manual/en/timezones.php>
        // ------------------------------------------------
        'timezone' => 'Asia/Shanghai',
    ],

    // -----------------------------------------------
    //     `db` => Database related configurations
    // -----------------------------------------------
    'db' => [

        // -----------------------------------------------
        //     *`default` => Default connection to use
        //     (should in the list of `conns` below)
        // -----------------------------------------------
        'default' => 'mysql_master_rw_0',

        // ---------------------------------------------
        //     *`conns` => Database connections list
        // ---------------------------------------------
        'conns' => [

            // -------------------------------------------------
            //     *{mysql_master_rw_0} => Connection name
            // -------------------------------------------------
            'mysql_master_rw_0' => [

                // -----------------------------------------
                //     *`driver` => Database driver type
                // -----------------------------------------
                'driver'  => 'mysql',

                // ------------------------------------
                //     *`host` => Connection's host
                //     (<IP|Domain:Port]>)
                // ------------------------------------
                'host'    => 'db',

                // ----------------------------------------
                //     *`user` => Connection's username
                // ----------------------------------------
                'user'    => 'lif',

                // ------------------------------------------
                //     *`passwd` => Connection's password
                // ------------------------------------------
                'passwd'  => 'lif',

                // ----------------------------------------
                //     *`dbname` => Database name
                //     (optional, used after connected)
                // ----------------------------------------
                'dbname'  => 'lif',

                // --------------------------------------------
                //     *`charset` => Connection charset
                //     (optional, used in whole connection)
                // --------------------------------------------
                'charset' => 'UTF8',
            ],

            'sqlite_local_dev' => [
                'driver' => 'sqlite',

                // -----------------------------------------------------------
                //     - `false` => default, can ignore (Optional)
                //     - `true`  => when driver is sqlite, if `memory` set
                //     to `true`, `path` will be ignored
                // -----------------------------------------------------------
                'memory' => false,

                // -------------------------------------------------------
                //     Sqlite database file path
                //     Start with project root path (`pathOf('root')`)
                // -------------------------------------------------------
                'path' => '/var/db/db.sqlite',

                // 'user'   => null,
                // 'passwd' => null,
            ],

            //  ...
        ],
    ],

    // ---------------------------------------------------
    //     `queue` => Queue job related configurations
    // ---------------------------------------------------
    'queue' => [

        // -----------------------------------------------------
        //     *`default` => Default queue connection to use
        //     (should in the list of `conns` below)
        // -----------------------------------------------------
        'default' => 'sqlite_queue',

        // -------------------------------------------------
        //     *`conns` => Queue medium connections list
        // -------------------------------------------------
        'conns' => [

            // ------------------------------------------
            //     *{sqlite_queue} => Connection name
            // ------------------------------------------
            'sqlite_queue' => [

                // ------------------------------------
                //     *`type` => Queue medium type
                // ------------------------------------
                'type'  => 'db',

                // -----------------------------------------------
                //     *`conn` => Queue medium connection name
                // -----------------------------------------------
                'conn'  => 'local_sqlite',

                // ------------------------------------
                //     *`table` => Queue table name
                // ------------------------------------
                'table' => 'queue_job',

                // ---------------------------------------------
                //     `defs` => Queue table definition
                //     `queue_default_defs_get()` <= default
                // ---------------------------------------------
                'defs'  => [
                    // ...
                ],
            ],

            // ...
        ],
    ],

    // ----------------------------------------------
    //     `log` => Logger related configurations
    // ----------------------------------------------

    'log' => [

        // -----------------------------------------------
        //     *`default` => Default logger to use
        //     (should in the list of `loggers` below)
        // -----------------------------------------------
        'default' => 'file_log',

        // ---------------------------------
        //     *`loggers` => Logger list
        // ---------------------------------
        'loggers' => [

            // -----------------------------------
            //     *{file_log} => Logger name
            // -----------------------------------
            'file_log' => [

                // ---------------------------------------
                //     *`driver` => Logger driver type
                // ---------------------------------------
                'driver' => 'file',

                // --------------------------------------------
                //     `path` => File logger used file path
                // --------------------------------------------
                'path' => 'lif.log',
            ],
        ],
    ],

    // ----------------------------------------------
    //     `email` => Mail related configurations
    // ----------------------------------------------
    'mail' => [

        // ------------------------------------------------
        //     *`default` => Default mail sender to use
        //     (should in the list of `senders` below)
        // ------------------------------------------------
        'default' => 'swiftmailer',
        
        // -------------------------------------
        //     *`senders` => Mail sender list
        // -------------------------------------
        'senders' => [

            // ------------------------------------------
            //     *{swiftmailer} => Mail sender name
            // ------------------------------------------
            'swiftmailer' => [

                // --------------------------------------------
                //     *`driver` => Mail sender driver type
                // --------------------------------------------
                'driver' => 'swiftmailer',

                // ------------------------------------------
                //     *`host` => Mail sender server host
                // ------------------------------------------
                'host' => 'smtp.qq.com',

                // ------------------------------------------
                //     *`port` => Mail sender server port
                // ------------------------------------------
                'port' => 465,

                // ----------------------------------------------
                //     *`account` => Mail sender used account
                // ----------------------------------------------
                'account'    => 'admin@example.com',

                // ----------------------------------------------------
                //     *`credential` => Mail sender used credential
                // ----------------------------------------------------
                'credential' => 'access_credential',

                // ----------------------------------------------
                //     *`sender_name` => Mail sender own name
                // ----------------------------------------------
                'sender_name' => 'no-reply',

                // ------------------------------------------------
                //     *`sender_email` => Mail sender own email
                // ------------------------------------------------
                'sender_email' => 'noreply@example.com',
                
                // ---------------------------------------------------------
                //     *`encryption` => Mail sender used encryption type
                //     - ssl
                //     - tls
                //     - null
                // ---------------------------------------------------------
                'encryption' => 'ssl',
            ],
        ],
    ],
];
```
