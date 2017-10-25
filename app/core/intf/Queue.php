<?php

// -----------------------------------
//     LiF queue public interfaces
// -----------------------------------

namespace Lif\Core\Intf;

use \Lif\Core\Intf\Job;

interface Queue
{
    public function __construct(array $config);

    public function checkConfig();

    // Queue job
    // Must setting current queue job id to `$job`
    public function push(Job $job) : Queue;

    // De-queue first job by earliest create time
    public function pop(array $queues = []);

    // De-queue job by job id
    public function out(int $id) : bool;

    // Delete job by it's queue name
    public function delete(array $queues = []) : bool;

    // Restart all failed jobs in current job related queue
    public function restart(array $queues = []);

    // Set queue jobs table `restart` flag to 1 for given queues
    public function setRestart(array $queues = []);

    // List queue jobs of given queues
    public function list(array $queues = []) : array;

    // Push job into given queue
    public function on(string $queue = 'default') : Queue;

    // Setting job try times
    public function try(int $times = 3) : Queue;

    // Setting job timeout to given seconds
    // (Timeout is the max execute time for each queue job)
    public function timeout(int $secs = 0) : Queue;

    // Hold current job's status
    // - Setting job `lock` => 1
    // - Increase tries times
    public function hold();

    // Release current job
    // - Reset `lock` => 0
    public function release();
}
