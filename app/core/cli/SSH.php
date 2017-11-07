<?php

namespace Lif\Core\Cli;

class SSH
{
    private $config = [];

    public function __construct(array $config = [])
    {
        $this
        ->prepare()
        ->setConfig($config);
    }

    public function setConfig(array $config) : SSH
    {
        if (legal_server($config)) {
            $this->config = $config;
        }

        return $this;
    }

    // Runtime requirements pre-check
    private function prepare() : SSH
    {
        // if (! fe('exec')) {
        //     excp('PHP function `exec()` was disabled.');
        // }

        return $this;
    }

    public function exec($cmds) {
        $cmds = build_cmds_with_env($cmds);
        
        $passwdStr = ('pswd' == $this->config['auth'])
        ? ':'.$this->config['pswd']
        : '';

        $privateKeyFile = ('pki' == $this->config['auth'])
        ? '-i '.$this->config['prik'].' '
        : '';

        $sshWithCmds = 'ssh -o StrictHostKeyChecking=no '
        .$privateKeyFile
        .$this->config['user']
        .$passwdStr
        .'@'
        .$this->config['host']
        .' -p '
        .$this->config['port']
        ." '{$cmds}'";

        return proc_exec($sshWithCmds);
    }
}
