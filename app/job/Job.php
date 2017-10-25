<?php

namespace Lif\Job;

class Job extends \Lif\Core\Abst\Job
{
    public function run() : bool
    {
        echo 'Test job is running...', PHP_EOL;

        return true;
    }
}
