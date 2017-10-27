<?php

namespace Lif\Core\Cli;

class Timeout
{
    private $pid = null;

    public function __construct(int $pid = null)
    {
        $this->setPid($pid);
    }

    public function setPid(int $pid = null) : Timeout
    {
        $this->pid = $pid;

        return $this;
    }

    public function resetPid() : Timeout
    {
        $this->pid = null;

        return $this;
    }

    public function kill() : bool
    {
        $status = true;

        if ($this->pid
            && is_integer($this->pid)
            && (0 < $this->pid)
        ) {
            $status = posix_kill($this->pid, SIGKILL);

            // if (! $status) {
            //     exit('Kill task process failed.');
            // }
        }

        return $status;
    }
}
