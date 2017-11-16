<?php

namespace Lif\Core\Cmd\Mdl;

use Lif\Core\Abst\Command;

class Add extends Command
{
    protected $intro  = 'Add a model class';
    protected $option = [
        '-N'     => 'setModelName',
        '--name' => 'setModelName',
    ];
    protected $desc = [
        'setModelName' => 'Name of new model',
    ];

    private $mdlName = null;

    public function fire()
    {   
        if (! ($model = ucfirst($this->mdlName))) {
            $this->fails('No name specified for model.');
        }

        $class = format_namespace($model);

        if (class_exists($_class = nsOf('mdl', $class, false))) {
            $this->fails('Model already exists: '.$_class);
        }

        if (!($tpl = pathOf('core', 'tpl/Model.mdl'))
            || !file_exists($tpl)
        ) {
            excp('Framework error: template `Model.mdl` not exists.');
        }

        $mdl = preg_replace_callback_array([
                '/__NS__/u' => function ($match) use ($_class) {
                    $arr = explode('\\', $_class);
                    $unset = count($arr) - 1;
                    unset($arr[$unset]);
                    return implode('\\', $arr);
                },
                '/__MDL_CLASS_NAME__/u' => function ($match) use ($class) {
                    $arr = explode('\\', $class);
                    $set = count($arr) - 1;
                    return ucfirst($arr[$set]);
                },
                '/__NAME__/u' => function ($match) use ($model) {
                    return camelcase2underline(ns2classname($model));
                },
            ],
            file_get_contents($tpl)
        );

        $__class = str_replace('\\', '/', $class);

        file_put_contents(pathOf('mdl', "{$__class}.php"), $mdl)
        ? $this->success('New model: '.$_class)
        : $this->fails('Creating model failed.');
    }

    protected function setModelName(string $name = null)
    {
        $this->mdlName = $name;
    }
}
