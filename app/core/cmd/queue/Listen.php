<?php

namespace Lif\Core\Cmd\Queue;

use Lif\Core\Abst\Command;

class Listen extends Command
{
    protected $intro = 'Listen on queue jobs';

    protected $option = [
        '-N'       => 'setName',
        '--name'   => 'setName',
        '-D'       => 'daemon',    // Overide global `-D` option
        '--daemon' => 'daemon',
    ];

    protected $desc = [
        'daemon'  => 'Listen queue jobs background',
        'setName' => 'Specific queue name to listen',
    ];

    private $daemon    = true;
    private $queueName = false;

    public function fire()
    {
        if ($this->daemon) {
            while (true) {
                echo date('Y-m-d H:i:s'), "\t";
                sleep(5);
            }
        }
    }

    protected function daemon($value) : void
    {
        if (is_null($value)
            || ('1' === $value)
            || ('true' === $value)
        ) {
            $this->daemon = true;
        } elseif (('0' === $value)
            || 'false' === $value
        ) {
            $this->daemon = false;
        }
    }

    protected function setName(string $value = null, string $option) : void
    {
        if (! $value) {
            excp(
                'Please specific the queue name for option '
                .escape_fields($option)
            );
        }

        $this->queueName = $value;
    }
}
