<?php

// ---------------------------
//     LiF Cli Application
// ---------------------------

namespace Lif\Core\Strategy;

use Lif\Core\Intf\Strategy;
use Lif\Core\Abst\{Container, Factory};

class Cli extends Container implements Strategy
{
    protected $argvs  = null;    // Actual arguments of current command
    protected $_argvs = null;    // Raw arguments of current command
    protected $cmd    = null;    // Command class of current command
    protected $_cmd   = null;    // Command namespace of current command

    public function fire() : Cli
    {
        $this
        ->setApp()
        ->load()
        ->parse()
        ->run();

        return $this;
    }

    protected function setApp() : Cli
    {
        $class = __CLASS__;
        if (!isset($GLOBALS['LIF_CLI'])
            || !($GLOBALS['LIF_CLI'] instanceof $class)
        ) {
            $GLOBALS['LIF_CLI'] = &$this;
        }

        return $this;
    }

    public function setArgvs($argvs)
    {
        if ('cli' == php_sapi_name()) {
            unset($argvs[0]);
        }
        $this->_argvs = $argvs;
        $this->argvs  = array_values($this->_argvs);

        return $this;
    }

    // Load cli helpers
    protected function load() : Cli
    {
        load(
            pathOf('aux').'cli.php',
            'Cli helper file'
        );

        return $this;
    }

    public function reset() : Cli
    {
        $this->argvs
        = $this->_argvs
        = $this->cmd
        = $this->_cmd
        = null;

        return $this;
    }

    protected function resetArgvs() : array
    {
        unset($this->argvs[0]);

        return $this->argvs = array_values($this->argvs);
    }

    protected function parse() : Cli
    {
        $_cmd = $this->getDefaultCommand();

        if ($this->argvs) {
            // Find out command and it's action
            if (! is_cmd_option($this->argvs[0])) {
                $_cmd = $this->argvs[0];
                if ($this->resetArgvs()) {
                    if (! is_cmd_option($this->argvs[0])) {
                        $this->resetArgvs();
                    }
                }
            }
        }

        $this->_cmd = $this->legalCmd($_cmd);

        return $this;
    }

    protected function getDefaultAction() : string
    {
        return 'fire';
    }

    protected function getDefaultCommand() : string
    {
        return 'cli';
    }

    protected function legalCmd(string $cmd) : string
    {
        if (! preg_match('/^[a-z\.]*$/u', $cmd)) {
            excp('Illegal command name: '.$cmd);
        } elseif (false === ($ns = if_cmd_exists(cmd2class($cmd)))) {
            excp('Command not exists: '.$cmd);
        }

        return $ns;
    }

    protected function run()
    {
        $handler   = $this->getDefaultAction();
        $this->cmd = Factory::make($this->_cmd);

        if (!$this->cmd
            || !($this->cmd instanceof \Lif\Core\Abst\Command)
        ) {
            excp('Illegal command: '.$this->_cmd);
        } elseif (! method_exists($this->cmd, $handler)) {
            excp('Command handler not exists');
        }

        return call_user_func_array([
                $this->cmd,
                $handler
            ], [
                $this->argvs
            ]
        );
    }

    public function __destruct()
    {
        unset($GLOBALS);
    }
}
