<?php

namespace Lif\Core\Cli;

class SSH
{
    private $config = [];

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;

        $this->prepare()->validate();
    }

    // Runtime requirements pre-check
    private function prepare() : SSH
    {
        if (! fe('exec')) {
            excp('PHP function `exec()` was disabled.');
        }

        return $this;
    }

    // Validate SSH server configs
    private function validate()
    {
        if (true !== ($err = validate($this->config, [
            'host' => 'need|host',
            'port' => ['int|min:1', 22],
            'auth' => ['in:pswd,ssh', 'ssh'],
            'user' => 'when:auth=pswd|string',
            'pswd' => 'when:auth=pswd|string',
            'rsa'  => 'when:auth=ssh|string'
        ]))) {
            excp('Illegal SSH server configs: '.$err);
        }
    }

    public function exec()
    {
    }
}
