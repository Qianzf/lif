<?php

namespace Lif\Core\Cmd;

class CmdAdd extends CMD
{
    protected $intro  = 'Add a user defined console command';
    protected $option = [
        '-N'     => 'setCMDName',
        '--name' => 'setCMDName',
    ];
    protected $desc = [
        'setCMDName' => 'Name of new command',
    ];

    private $cmdName = null;

    public function fire()
    {   
        if (! ($command = ucfirst($this->cmdName))) {
            $this->fails('No name specified for command.');
        }

        $class = format_ns($command);

        if (class_exists($_class = nsOf('cmd', $class, false))) {
            $this->fails('Command already exists: '.$_class);
        }

        if (!($tpl = pathOf('core', 'tpl/Command.cmd'))
            || !file_exists($tpl)
        ) {
            excp('Framework error: template `Command.cmd` not exists.');
        }

        $cmd = preg_replace_callback_array([
                '/__NS__/u' => function ($match) use ($_class) {
                    $arr = explode('\\', $_class);
                    $unset = count($arr) - 1;
                    unset($arr[$unset]);
                    return implode('\\', $arr);
                },
                '/__CMD_CLASS_NAME__/u' => function ($match) use ($class) {
                    $arr = explode('\\', $class);
                    $set = count($arr) - 1;
                    return ucfirst($arr[$set]);
                },
            ],
            file_get_contents($tpl)
        );

        $__class = str_replace('\\', '/', $class);

        file_put_contents(pathOf('cmd', "{$__class}.php"), $cmd)
        ? $this->success('New command: '.$_class)
        : $this->fails('Adding command failed.');
    }

    protected function setCMDName(string $name = null)
    {
        $this->cmdName = $name;
    }
}
