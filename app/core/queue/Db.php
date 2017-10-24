<?php

// -------------------------
//     LiF Queue Engine
// -------------------------

namespace Lif\Core\Queue;

use \Lif\Core\Intf\{Queue, Job};

class Db implements Queue
{
    private $conn  = null;
    private $table = null;
    private $queue = null;

    public function __construct(array $config)
    {
        if (true !== ($err = legal_and($config, [
            'conn_name'  => ['need|string', &$this->conn],
            'conn_table' => ['need|string', &$this->table],
        ]))) {
            excp('Illegal database queue config: '.$err);
        }

        $this->queue = db($this->conn)->table($this->table);
    }

    public function in(Job $job)
    {
    }

    public function out()
    {
    }
}
