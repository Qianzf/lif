<?php

// -------------------------------------
//     Queue related init operations
// -------------------------------------

namespace Lif\Core\Traits;

use Lif\Core\Abst\Factory;
use Lif\Core\Intf\Queue as QueueMedium;

trait Queue
{
    protected $config = [];

    public function __construct()
    {
        $this->prepare();
    }

    protected function prepare() : self
    {
        $this->config = conf('queue');

        if (true !== ($err = validate($this->config, [
            'type'  => 'need|string',
        ]))) {
            excp(sysmsg($err));
        }

        switch ($this->config['type']) {
            case 'db':
                break;
            default:
                excp(
                    'Queue type not supported yet: '
                    .$this->config['type']
                );
                break;
        }

        legal_or($this->config, [
            'defs' => ['need|array', queue_default_defs_get()],
        ]);

        return $this;
    }

    protected function getQueue() : QueueMedium
    {
        return Factory::make(
            $this->config['type'],
            nsOf('queue'),
            $this->config
        );
    }
}
