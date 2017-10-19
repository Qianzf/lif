<?php

// --------------------------------------
//     Lif Default core command class
// --------------------------------------

namespace Lif\Core\Cmd;

class Command extends CMD
{
    protected $intro  = 'LiF default command';
    protected $option = [
        '-V'          => 'version',
        '--version'   => 'version',
        '-C'          => 'cache',
        '--cache'     => 'cache',
        '-L'          => 'list',
        '--list'      => 'list',
        '--list-all'  => 'list',
        '--list-core' => 'list',
        '--list-user' => 'list',
        '--cmds-all'  => 'list',
        '--cmds-core' => 'list',
        '--cmds-user' => 'list',
        '--cmds'      => 'list',
    ];
    protected $desc = [
        'version' => 'Get version of LiF application',
        'list'    => 'List available types of commands: all, core, user',
        'cache'   => 'Cache CLI output commands text',
    ];

    public function fire()
    {
        if (!$this->params && !$this->options) {
            return output($this->lif()
                .$this->help(true)
            );
        }
    }

    public function help($string = false)
    {
        $help = $this->usage()
        .$this->options()
        .$this->cmds();

        return $string ? $help : output($help);
    }

    protected function cache()
    {
        dd(get_user_cmds());
    }

    protected function version()
    {
        return output($this->getColoredVersionText());
    }

    protected function getColoredVersionText()
    {
        return color(get_lif_ver(), 'PURPLE');
    }

    protected function getColoredAuthorText()
    {
        return color('ckwongloy@gmail.com', 'DARK_GRAY');
    }

    protected function lif() : string
    {
        return segstr(color('Li Framework ', 'LIGHT_BLUE')
            .$this->getColoredVersionText()
            .space_indent()
            .$this->getColoredAuthorText()
        );
    }

    protected function list(string $val = null, string $option)
    {
        $scope = 'all';
        switch ($option) {
            case '--list-core':
            case '--cmds-core': {
                $scope = 'core';
            } break;
            case '--list-user':
            case '--cmds-user': {
                $scope = 'user';
            } break;
            case '-L':
            case '--list':
            case '--cmds': {
                if (in_array($val, ['core', 'user'])) {
                    $scope = $val;
                }
            } break;
            case '--list-all':
            case '--cmds-all':
            default: {
                $scope = 'all';
            } break;
        }
        return output($this->cmds($scope));
    }

    protected function cmds(string $scope = 'all') : string
    {
        switch ($scope) {
            case 'core': {
                return segstr(color('Core Commands: ', 'LIGHT_PURPLE'))
                .get_core_cmds(true);
            } break;
            case 'user': {
                return segstr(color('User Commands: ', 'LIGHT_PURPLE'))
                .get_user_cmds(true);
            } break;
            case 'all':
            default: {
                return segstr(color('Commands: ', 'LIGHT_PURPLE'))
                .get_all_cmds(true);
            } break;
        }
    }
}
