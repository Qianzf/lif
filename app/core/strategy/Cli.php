<?php

// ---------------------------
//     LiF Cli Application
// ---------------------------

namespace Lif\Core\Strategy;

use Lif\Core\Intf\Strategy;
use Lif\Core\Abst\{Container, Factory};

class Cli extends Container implements Strategy
{
    protected $argvs   = null;    // Actual arguments of current command
    protected $_argvs  = null;    // Raw arguments of current command
    protected $cmd     = null;    // Command class of current command
    protected $_cmd    = null;    // Command namespace of current command
    protected $act     = null;    // Command class action of current command
    protected $debug   = false;

    public function fire() : Cli
    {
        $this
        ->setInstance()
        ->load()
        ->parse()
        ->run();

        return $this;
    }

    protected function setInstance() : Cli
    {
        if (!isset($GLOBALS['LIF_CLI'])
            || !($GLOBALS['LIF_CLI'] instanceof Lif\Core\Strategy\Cli)
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

    protected function resetArgvs() : array
    {
        unset($this->argvs[0]);
        return $this->argvs = array_values($this->argvs);        
    }

    protected function isOption(string $arg) : bool
    {
        
    }

    protected function parse() : Cli
    {
        $this->_cmd = $this->getDefaultCommand();
        $this->act  = $this->getDefaultAction();

        if ($this->argvs) {
            // Find out command and it's action
            if (! is_cmd_option($this->argvs[0])) {
                $this->_cmd = $this->argvs[0];
                if ($this->resetArgvs()) {
                    if (! is_cmd_option($this->argvs[0])) {
                        $this->act = $this->legalAct($this->argvs[0]);
                        $this->resetArgvs();
                    }
                }
            }
        }

        $this->_cmd = $this->legalCmd($this->_cmd);

        return $this;
    }

    protected function getDefaultAction() : string
    {
        return 'fire';
    }

    protected function getDefaultCommand() : string
    {
        return 'lif';
    }

    protected function legalAct(string $act) : string
    {
        if (! preg_match('/^\w*$/u', $act)) {
            excp('Illegal command action name.');
        }

        return $act;
    }

    protected function legalCmd(string $cmd) : string
    {
        if (! preg_match('/^[a-z_]*$/u', $cmd)) {
            excp('Illegal command name.');
        }

        $ns = get_core_cmd_class($cmd, $this->act);

        if (! class_exists($ns)) {
            excp('Command class not exists: '.$ns);
        }

        return $ns;
    }

    protected function run()
    {
        $this->cmd = Factory::make($this->_cmd);

        if (!$this->cmd || !is_object($this->cmd)) {
            excp('Illegal command: '.$this->_cmd);
        } elseif (! method_exists($this->cmd, $this->act)) {
            excp('Command handler not exists: '.$this->act);
        }

        return call_user_func_array([
                $this->cmd,
                $this->act
            ], [
                $this->argvs
            ]
        );
    }

    public function setDebug(bool $debug)
    {
        return $this->debug = $debug;
    }

    public function __destruct()
    {
        $GLOBALS = [];
    }
}
