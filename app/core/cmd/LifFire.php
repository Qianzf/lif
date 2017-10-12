<?php

namespace Lif\Core\Cmd;

class LifFire extends Command
{
    protected $intro  = 'LiF Default command action';
    protected $option = [
        '-V'        => 'version',
        '--version' => 'version',
    ];
    protected $desc = [
        'version' => 'Get version of LiF application',
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
            excp('No arguments for command: '.color($this->name(), 'BROWN'));
        }

        $this->withOptions($options);

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

    protected function getColoredCmdsText(array $cmds) : string
    {
        return segstr(color('help', 'LIGHT_GREEN'));
    }

    protected function cmds() : string
    {
        return segstr(color('Commands: ', 'LIGHT_PURPLE'))
        .$this->getColoredCmdsText(get_all_cmds());
    }
}
