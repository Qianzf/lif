<?php

namespace Lif\Job;

class Job extends \Lif\Core\Abst\Job
{
    public function run() : bool
    {
        echo 'Job ', posix_getpid(), ' is running...', PHP_EOL;
        $i = 0;
        while (++$i < 5) {
            sleep(1);
        }
        echo 'Job ', posix_getpid(), ' is stoped', PHP_EOL;

        return true;
    }
}
