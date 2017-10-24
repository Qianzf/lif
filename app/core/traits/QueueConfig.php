<?php

namespace Lif\Core\Traits;

use Lif\Core\Abst\Factory;

trait QueueConfig
{
    protected $config = [];
    protected $tbDefs = [
        'id',
        'queue',
        'detail',
        'tried',
        'create_at',
        'finish_at',
        'restart',
        'retry',
    ];

    public function __construct()
    {
        $this->config = conf('queue');

        $this->__check();
    }

    protected function __check()
    {
        if (true !== ($err = validate($this->config, [
            'conn_type'  => 'need|string',
            'conn_name'  => 'need|string',
            'conn_table' => 'need|string',
        ]))) {
            excp(sysmsg($err));
        }

        switch ($this->config['conn_type']) {
            case 'db':
                $driver = conf('db')
                ['conns'][$this->config['conn_name']]['driver']
                ?? null;

                if (is_null($driver)) {
                    excp('Illegal queue driver name.');
                }
                $existsChecker = 'checkIfTableExistsWhen'.ucfirst($driver);
                $defChecker    = 'checkIfTableDefLegalWhen'.ucfirst($driver);

                if (! method_exists($this, $existsChecker)) {
                    excp('Queue driver handler not found (1).');
                }
                if (true !== call_user_func([$this, $existsChecker])) {
                    excp(
                        'Queue table not exists: '
                        .$this->config['conn_name']
                        .'.'
                        .$this->config['conn_table']
                    );
                }

                if (! method_exists($this, $defChecker)) {
                    excp('Queue driver handler not found (2).');
                }
                if (true !== call_user_func([$this, $defChecker])) {
                    excp('Illegal queue table definition.');
                }
                break;
            default:
                excp(
                    'Queue type not supported yet: '
                    .$this->config['conn_type']
                );
                break;
        }
    }

    protected function getQueue()
    {
        return Factory::make(
            $this->config['conn_type'],
            nsOf('queue'),
            $this->config
        );
    }

    protected function checkIfTableDefLegalWhenMysql()
    {
        $table = db($this->config['conn_name'])
        ->raw('DESC '.$this->config['conn_table']);

        return $this->checkIfQueueTableMissingFiedls(
            array_column($table, 'Field')
        );
    }

    protected function checkIfTableDefLegalWhenSqlite()
    {
        $table = db($this->config['conn_name'])
        ->raw("PRAGMA table_info({$this->config['conn_table']})");

        return $this->checkIfQueueTableMissingFiedls(
            array_column($table, 'name')
        );
    }

    protected function checkIfQueueTableMissingFiedls(array $result) : bool
    {
        // $result count can more than $this->tbDefs
        // Otherwise not
        if ($missings = array_diff($this->tbDefs, $result)) {
            $missing  = implode(', ', $missings);

            excp(
                'Illegal queue table definition, missing fields: '
                .linewrap(2)
                .$missing
            );
        }

        return true;
    }

    protected function checkIfTableExistsWhenMysql()
    {
        return (
            count((
                db($this->config['conn_name'])
                ->raw(
                    'SHOW TABLES LIKE ?', [
                        $this->config['conn_table']
                    ])
            )) === 1
        );
    }

    protected function checkIfTableExistsWhenSqlite()
    {
        return (
            db($this->config['conn_name'])
            ->table('sqlite_master')
            ->whereTypeName('table', $this->config['conn_table'])
            ->count() === 1
        );
    }
}
