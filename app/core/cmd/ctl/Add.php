<?php

namespace Lif\Core\Cmd\Ctl;

use Lif\Core\Abst\Command;

class Add extends Command
{
    protected $intro  = 'Add a controller class';
    protected $option = [
        '-N'     => 'setCtlName',
        '--name' => 'setCtlName',
    ];
    protected $desc = [
        'setCtlName' => 'Name of new controller',
    ];

    private $ctlName = null;

    public function fire()
    {   
        if (! ($controller = ucfirst($this->ctlName))) {
            $this->fails('No name specified for controller.');
        }

        $class = format_ns($controller);

        if (class_exists($_class = nsOf('ctl', $class, false))) {
            $this->fails('Controller already exists: '.$_class);
        }

        if (!($tpl = pathOf('core', 'tpl/Controller.ctl'))
            || !file_exists($tpl)
        ) {
            excp('Framework error: template `Controller.ctl` not exists.');
        }

        $ctl = preg_replace_callback_array([
                '/__NS__/u' => function ($match) use ($_class) {
                    $arr = explode('\\', $_class);
                    $unset = count($arr) - 1;
                    unset($arr[$unset]);
                    return implode('\\', $arr);
                },
                '/__CTL_CLASS_NAME__/u' => function ($match) use ($class) {
                    $arr = explode('\\', $class);
                    $set = count($arr) - 1;
                    return ucfirst($arr[$set]);
                },
            ],
            file_get_contents($tpl)
        );

        $__class = str_replace('\\', '/', $class);

        file_put_contents(pathOf('ctl', "{$__class}.php"), $ctl)
        ? $this->success('New controller: '.$_class)
        : $this->fails('Creating controller failed.');
    }

    protected function setCtlName(string $name = null)
    {
        $this->ctlName = $name;
    }
}
