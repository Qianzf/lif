<?php

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

    public static function pdo($conn = null)
    {
        $db = conf('db');
        if (!exists($db, 'default')) {
            excp('Default database connection not set.');
        }
        $connName = $conn ?? $db['default'];

        $new = (
            !exists(self::$conns, $connName) ||
            !is_object(self::$conns[$connName])
        );

        if ($new) {
            try {
                if (!exists($db, 'conns')) {
                    excp('Database connections not set or is empty.');
                }
                if (!exists($db['conns'], $connName)) {
                    excp(
                        'Database connection `'.
                        $connName.
                        '` not set or is empty.'
                    );
                }
                $dbConn = $db['conns'][$connName];
                if (!exists($dbConn, [
                    'host',
                    'driver',
                    'user',
                    'passwd',
                ])) {
                    excp(
                        'Missing necessary configurations for connection `'
                        .$connName.'`'
                    );
                }
                self::$conns[$connName] = new LDO(
                    build_pdo_dsn($dbConn),
                    $dbConn['user'],
                    $dbConn['passwd']
                );
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
