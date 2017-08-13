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
        if (!$conn
            && (
                !($defaultConn = exists($db, 'default'))
                || !is_string($defaultConn)
            )
        ) {
            excp('Default database connection not set.');
        }
        $connName = ($conn && is_string($conn)) ? $conn : $defaultConn;

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
                $dbConn['name'] = $connName;    // require connection name

                self::$conns[$connName] = create_ldo($dbConn);
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
