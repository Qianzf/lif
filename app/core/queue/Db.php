<?php

// -----------------------------
//     Database queue engine
// -----------------------------

namespace Lif\Core\Queue;

use \Lif\Core\Intf\{Queue, Job};

class Db implements Queue
{
    private $config = [];
    private $queue  = null;    // Queue engine object
    private $job    = [];      // Queue job record, not Job class

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

    public function push(Job $job) : Queue
    {
        $this->job = [
            'queue'     => 'default',
            'detail'    => serialize($job),
            'try'       => 3,
            'tried'     => 0,
            'retried'   => 0,
            'create_at' => date('Y-m-d H:i:s'),
            'timeout'   => 0,
            'restart'   => 0,
            'lock'      => 0,
        ];

        $id = $this->queue()->insert($this->job);

        if ($id < 1) {
            excp('Enqueue job failed.');
        }

        $this->job['id'] = $id;

        return $this;
    }

    public function pop(array $queues = [])
    {
        $job = $this->queue(true)
        ->whereLock(0)
        ->where(function ($db) {
            $db->whereTried('<', $db->native('`try`'));
        })
        ->sort([
            'create_at' => 'asc',
        ]);

        if ($queues) {
            $job = $job->whereQueue($queues);
        }

        if (! ($job = $job->first())) {
            return false;
        }

        if (!isset($job['detail'])
            || !($_job = unserialize($job['detail']))
            || !($_job instanceof Job)
        ) {
            excp('Illegal queue job.');
        }

        $this->job = $job ?? null;

        return $_job;
    }

    public function out(int $id = null) : bool
    {
        $id = $id ?? $this->requireJobId();

        $status = $this->queue(true)
        ->whereId($id)
        ->delete();

        if (0 > $status) {
            excp('Delete queue job by job id failed: '.$id);
        }

        return ($status >= 0);
    }

    public function delete(array $queues = []) : bool
    {
        if (! $queues) {
            $queues = 'default';
        }

        $status = $this->queue()->reset()
        ->whereQueue($queues)
        ->delete();

        if (0 > $status) {
            excp(
                'Delete queue (with jobs) by queue names failed: '
                .implode(', ', $queues)
            );
        }

        return ($status >= 0);
    }

    public function getJobId()
    {
        return $this->job['id'] ?? null;
    }

    public function getJob() : array
    {
        return $this->job ? $this->job : [];
    }

    public function requireJob() : array
    {
        if ($job = $this->getJob()) {
            return $job;
        }

        excp('Missing job.');
    }

    public function requireJobId()
    {
        if ($id = $this->getJobId()) {
            return $id;
        }

        excp('Missing job id.');
    }

    public function setRestart(array $queues = [])
    {
        $queue = $this
        ->queue()
        ->whereTried('>=', db()->native('try'));

        if ($queues) {
            $queue = $queue->whereQueue($queues);
        }
        
        $affected = $queue->update([
            'restart' => 1,
        ]);

        return ($affected >= 0);
    }

    public function restart(array $queues = [])
    {
        $status  = $this->queue()
        ->whereTried('>=', db()->native('try'))
        ->whereRestart(1)
        ->update([
            'restart' => 0,
            'tried'   => 0,
            'lock'    => 0,
            'retried' => db()->native('retried + 1'),
        ]);

        if ($status < 0) {
            excp('Restart failed queue jobs failed.');
        }

        return ($status >= 0);
    }

    public function list(array $queues = []) : array
    {
    }

    public function on(string $queue = 'default') : Queue
    {
        $status = $this->queue()
        ->whereId($this->requireJobId())
        ->update([
            'queue' => $queue,
        ]);

        if ($status < 0) {
            excp('Set job on queue '.$queue.' failed.');
        }

        return $this;
    }

    public function try(int $times = 3) : Queue
    {
        $status = $this->queue()
        ->whereId($this->requireJobId())
        ->update([
            'try' => $times,
        ]);

        if ($status < 0) {
            excp('Set job try times to '.$times.' failed.');
        }

        return $this;
    }

    public function timeout(int $secs = 0) : Queue
    {
        $status = $this->queue()
        ->whereId($this->requireJobId())
        ->update([
            'timeout' => $secs,
        ]);

        if ($status < 0) {
            excp('Set job timeout to '.$secs.'s failed.');
        }

        return $this;
    }

    public function hold()
    {
        $status = $this->queue()
        ->whereId($this->requireJobId())
        ->update([
            'lock'  => 1,
            'tried' => db()->native('`tried` + 1'),
        ]);

        if ($status < 0) {
            excp('Hold current job status failed.');
        }

        return ($status >= 0);
    }

    public function release()
    {
        $status = $this->queue()
        ->whereId($this->requireJobId())
        ->update([
            'lock' => 0,
        ]);

        if ($status < 0) {
            excp('Release current job status failed.');
        }

        return ($status >= 0);
    }

    protected function queue(bool $flushConn = false)
    {
        if (!$this->queue || $flushConn) {
            $this->queue = db(
                $this->config['conn'],
                $flushConn
            )
            ->table($this->config['table']);
        }

        return $this->queue;
    }

    protected function checkIfTableDefLegalWhenMysql()
    {
        $table = db($this->config['conn'])
        ->raw('DESC '.$this->config['table']);

        return $this->checkIfQueueTableMissingFields(
            array_column($table, 'Field')
        );
    }

    protected function checkIfTableDefLegalWhenSqlite()
    {
        $table = db($this->config['conn'])
        ->raw("PRAGMA table_info({$this->config['table']})");

        return $this->checkIfQueueTableMissingFields(
            array_column($table, 'name')
        );
    }

    protected function checkIfQueueTableMissingFields(array $result) : bool
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
