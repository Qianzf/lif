<?php

namespace \Lif\Core\Abst;

abstract class Timer
{
    protected $type  = 'INTERVAL';    // Type of job
    protected $secs  = 1;     // Seconds of job to be executed
    protected $start = null;  // Timestamp of job being created
    protected $exec  = 0;     // Times of current job being executed
    protected $status = 0;    // Status of job execution result

    public function __construct(
    ) {
        $this->createAt = time();
    }

    protected function run()
    {
    }

    protected function isValidate()
    {
        return in_array($this->type, [
            'TIMEOUT',
            'INTERVAL',
        ]);
    }
}
