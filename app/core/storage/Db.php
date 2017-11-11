<?php

// -------------------------------------------------
//     Manage database instances and connections
// -------------------------------------------------

namespace Lif\Core\Storage;

class Db
{
    protected static $conns = [
    ];

    private function __construct()
    {
    }

    public static function __getStatic($name)
    {
        return self::$name();
    }

    public static function __callStatic($name, $args)
    {
        excp(
            'Method `'.$name.'()` not exists in '.(static::class)
        );
    }

    public static function ldo(array $params = [])
    {
        return self::get($params);
    }

    public static function pdo(array $params = [])
    {
        return self::get($params, 'pdo');
    }

    public static function get(array $params = [], $type = 'ldo')
    {
        $db = conf('db');
        // string $conn = null, bool $force = false
        $conn  = $params['conn']  ?? null;
        $flush = $params['flush'] ?? false;
        
        if (!$conn && (
            !($defaultConn = exists($db, 'default'))
            || !is_string($defaultConn)
        )) {
            excp('Default database connection not set.');
        }
        $connName = ($conn && is_string($conn)) ? $conn : $defaultConn;

        $new = ($flush
            || !exists(self::$conns, $connName)
            || !is_object(self::$conns[$connName])
        );

        if ($new) {
            try {
                if (! exists($db, 'conns')) {
                    excp('Database connections not set or is empty.');
                }
                if (! exists($db['conns'], $connName)) {
                    excp('Database connection `'
                        .$connName
                        .'` not set or is empty.'
                    );
                }
                $dbConn = $db['conns'][$connName];
                $dbConn['name'] = $connName;    // require connection name
                $creator = 'create_'.$type;

                if (! function_exists($creator)) {
                    excp(
                        'Database connection creator not exists: '
                        .$creator
                    );
                }

                self::$conns[$connName] = call_user_func($creator, $dbConn);
            } catch (\PDOException $pdoE) {
                exception($pdoE);
            }
        }

        return self::$conns[$connName];
    }

    public static function conns($conn = null)
    {
        if ($conn) {
            if ($connObj = exists(self::$conns, $conn)) {
                return $connObj;
            }

            excp('No connection for `'.$conn.'`.');
        }

        return self::$conns;
    }
}
