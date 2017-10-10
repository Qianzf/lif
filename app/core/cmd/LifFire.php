<?php

namespace Lif\Core\Cmd;

class LifFire extends Command
{
    protected $name   = 'lif:fire';
    protected $desc   = 'LiF Default command action';
    protected $option = [
        '-V'        => 'version',
        '--version' => 'version',
        '-H'        => 'help',
        '--help'    => 'help',
    ];
    protected $_desc  = [
        'version' => 'Get current LiF version',
        'help'    => 'Output help message for current command',
    ];

    public function fire(array $params)
    {
        if (! $params) {
            return output(
                $this->lif()
                .$this->usage()
                .$this->options()
                .$this->cmds()
            );
        }

        $this->parse($params, $options, $args);

        if ($args) {
            excp('No arguments for command: '.$this->name);
        }

        foreach ($options as $option) {
            if (! in_array($option, array_keys($this->option))) {
                excp('Option not exists: '.$option);
            } elseif (! method_exists($this, $this->option[$option])) {
                excp('Option handler not exists: '.$this->option[$option]);
            }

            call_user_func_array([
                $this,
                $this->option[$option]
            ], []);
        }

        return $this;
    }

    public function help()
    {
        return output(
            $this->usage()
            .$this->options()
            .$this->cmds()
        );
    }

    protected function version()
    {
        return output($this->getColoredVersionText());
    }

    protected function getColoredVersionText()
    {
        return color(get_lif_ver(), 'PURPLE');
    }

    protected function lif() : string
    {
        return segstr(
            color('Li Framework ', 'LIGHT_BLUE')
            .$this->getColoredVersionText()
        );
    }

    protected function cmds() : string
    {
        return segstr(color('Commands: ', 'LIGHT_PURPLE'));
    }
}
