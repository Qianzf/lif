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

        $class = underline2camelcase($command);

        if (($_class = nsOf('cmd', $class)) && class_exists($_class)) {
            $this->fails('Command already exists: '.$_class);
        }

        if (!($tpl = pathOf('core', 'tpl/Command.cmd'))
            || !file_exists($tpl)
        ) {
            excp('Framework error: template `Command.cmd` not exists.');
        }

        $cmd = preg_replace_callback_array([
                '/__CMD_CLASS_NAME__/u' => function ($match) use ($class) {
                    $arr = explode('\\', $class);
                    $set = count($arr) - 1;
                    return ucfirst($arr[$set]);
                },
            ],
            file_get_contents($tpl)
        );

        file_put_contents(pathOf('cmd', "{$class}.php"), $cmd)
        ? $this->success('New command: '.$_class)
        : $this->fails('Adding command failed.');
    }

    protected function setCMDName(string $name = null)
    {
        $this->cmdName = $name;
    }
}
