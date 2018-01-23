<?php

// -------------------------------------
//     Queue related init operations
// -------------------------------------

namespace Lif\Core\Traits;

use Lif\Core\Abst\Factory;
use Lif\Core\Intf\{Queue as QueueMedium, Job};

trait Queue
{
    private $config    = [];
    private $queues    = [];
    private $queue     = null;    // Queue engine object
    private $queueConn = null;
    private $job       = null;

    protected function prepare() : self
    {
        $this->config = conf('queue');

        if (true !== ($err = validate($this->config, [
            'default' => 'need|string',
            'conns'   => 'need|array',
        ]))) {
            excp('Illegal queue configs: '.sysmsg($err));
        }
        if (true !== ($err = validate($this->config['conns'], [
            $this->config['default'] => 'need|array',
        ]))) {
            excp('Missing or empty queue connection configs: '.sysmsg($err));
        }
        $conn = &$this->config['conns'][$this->config['default']];
        if (true !== ($err = validate($conn, [
            'type'  => 'need|string',
            'conn'  => 'need|string',
            'table' => 'need|string',
        ]))) {
            excp('Illegal queue connection configs: '.sysmsg($err));
        }

        switch ($conn['type']) {
            case 'db':
                break;
            default:
                excp(
                    'Queue type not supported yet: '
                    .$conn['type']
                );
                break;
        }

        legal_or($conn, [
            'defs' => ['need|array', queue_default_defs_get()],
        ]);

        unset($conn);

        return $this;
    }

    protected function getQueue() : QueueMedium
    {
        if ($this->queue && ($this->queue instanceof QueueMedium)) {
            return $this->queue;
        }

        $conn       = $this->queueConn ?? $this->config['default'];
        $connConfig = $this->config['conns'][$conn];

        return $this->queue = Factory::make(
            $connConfig['type'],
            nsOf('queue'),
            $connConfig
        );
    }

    protected function getJob()
    {
        if (! $this->job) {
            $this->job = $this->getQueue()->requireJob();
        }

        return $this->job;
    }

    protected function getJobTimeout() : int
    {
        return intval($this->getJob()['timeout'] ?? 0);
    }

    protected function getFirstJob()
    {
        $job = $this->getQueue()->pop($this->queues);

        $this->job = $this->getQueue()->getJob();

        return $job;
    }

    protected function holdCurrentJob()
    {
        return $this->getQueue()->hold();
    }

    protected function releaseCurrentJob()
    {
        return $this->getQueue()->release();
    }

    protected function restartFailedJobs()
    {
        return $this->getQueue()->restart($this->queues);
    }

    protected function outOfQueue(int $id = null) : bool
    {
        return $this->getQueue()->out($id);
    }

    // Push job into queue
    public function enqueue(Job $job) : QueueMedium
    {
        return $this->getQueue()->push($job);
    }

    protected function setQueues(string $queues = null, string $option) : void
    {
        if (! $queues) {
            excp(
                'Please specific the queue names for option '
                .escape_fields($option)
            );
        }

        $data = [];
        if ($params = explode(',', $queues)) {
            array_walk($params, function ($item) use (&$data) {
                if ($item = trim($item)) {
                    $data[] = $item;
                }
            });
        }

        $this->queues = $data;
    }

    protected function setQueueConn(string $conn = null, string $option)
    {
        if (! $conn) {
            excp(
                'Please specific the queue connection name for option '
                .escape_fields($option)
            );
        }

        if (! isset($this->config['conns'][$conn])) {
            excp(
                'Queue connection not exists in configs: '
                .$conn
            );
        }

        $this->queueConn = $conn;
    }
}
