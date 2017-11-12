<?php

namespace Lif\Core\Storage\SQL\Mysql;

trait Grammers
{
    private $formats = [
        'FIXED',
        'DYNAMIC',
        'DEFAULT',
    ];
    private $storages = [
        'DISK',
        'MEMORY',
        'DEFAULT',
    ];
    private $grammers  = [
        'integer' => [
            'BIT',
            'TINYINT',
            'SMALLINT',
            'MEDIUMINT',
            'INT',
            'INTEGER',
            'BIGINT',
        ],
        'float' => [
            'REAL',
            'DOUBLE',
            'FLOAT',
            'DECIMAL',
            'NUMERIC',
        ],
        'string' => [
            'CHAR',
            'VARCHAR',
            'TINYTEXT',
            'TEXT',
            'MEDIUMTEXT',
            'LONGTEXT',
        ],
        'time' => [
            'DATE',
            'TIME',
            'TIMESTAMP',
            'DATETIME',
            'YEAR',
        ],
        'binary' => [
            'BINARY',
            'VARBINARY',
            'TINYBLOB',
            'BLOB',
            'MEDIUMBLOB',
            'LONGBLOB',
        ],
        'enum' => [
            'ENUM',
        ],
        'set' => [
            'SET',
        ],
        'json' => [
            'JSON',
        ],
    ];
}
