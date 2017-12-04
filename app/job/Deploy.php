<?php

namespace Lif\Job;

use \Lif\Mdl\Task;

class Deploy extends \Lif\Core\Abst\Job
{
    protected $task = null;

    public function setTask(int $task)
    {
        $this->task = $task;

        return $this;
    }

    // 1. Find task related project `A` and it's deploy script
    // 2. Select one available environment `B` for project `A`
    //    according to current task status
    // 3. Find server with project path `C` for environment `B`
    // 4. Connect to server and pull task branch and execute deploy script
    // 5. Switch deploy status and assign to proper person with remarks
    public function run() : bool
    {
        $task = new Task($this->task);

        echo time(), ' => ', $task->origin()->title;

        return true;
    }
}
