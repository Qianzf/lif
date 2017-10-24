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

    public function in(Job $job);

    public function out();
}
