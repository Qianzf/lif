<?php

// -------------------------
//     LiF Queue Engine
// -------------------------

namespace Lif\Core\Queue;

use \Lif\Core\Intf\{Queue, Job};

class Db implements Queue
{
    private $config = [];
    private $queue  = null;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->checkConfig();
    }

    public function checkConfig()
    {
        if (true !== ($err = validate($this->config, [
            'conn'  => 'need|string',
            'table' => 'need|string',
            'defs'  => 'need|array',
        ]))) {
            excp('Illegal database queue config: '.$err);
        }

        $driver = conf('db')
        ['conns'][$this->config['conn']]['driver']
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
                .$this->config['conn']
                .'.'
                .$this->config['table']
            );
        }

        if (! method_exists($this, $defChecker)) {
            excp('Queue driver handler not found (2).');
        }
        if (true !== call_user_func([$this, $defChecker])) {
            excp('Illegal queue table definition.');
        }
    }

    public function in(Job $job)
    {
    }

    public function out()
    {
    }

    protected function queue()
    {
        if ($this->queue) {
            $this->queue = db($this->config['conn'])
            ->table($this->config['table']);
        }

        return $this->queue;
    }

    protected function checkIfTableDefLegalWhenMysql()
    {
        $table = db($this->config['conn'])
        ->raw('DESC '.$this->config['table']);

        return $this->checkIfQueueTableMissingFiedls(
            array_column($table, 'Field')
        );
    }

    protected function checkIfTableDefLegalWhenSqlite()
    {
        $table = db($this->config['conn'])
        ->raw("PRAGMA table_info({$this->config['table']})");

        return $this->checkIfQueueTableMissingFiedls(
            array_column($table, 'name')
        );
    }

    protected function checkIfQueueTableMissingFiedls(array $result) : bool
    {
        // $result count can more than $tbDefs
        // Otherwise not
        if ($missings = array_diff($this->config['defs'], $result)) {
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
                db($this->config['conn'])
                ->raw(
                    'SHOW TABLES LIKE ?', [
                        $this->config['table']
                    ])
            )) === 1
        );
    }

    protected function checkIfTableExistsWhenSqlite()
    {
        return (
            db($this->config['conn'])
            ->table('sqlite_master')
            ->whereTypeName('table', $this->config['table'])
            ->count() === 1
        );
    }
}
