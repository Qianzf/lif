<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Add extends Command
{
    protected $intro = 'Add a dit into vcd';

    protected $option = [
        '-N'      => 'setDitName',
        '--name'  => 'setDitName',
        '-T'      => 'setTableName',
        '--table' => 'setTableName',
    ];
    protected $desc = [
        'setDitName'   => 'Name of dit to be created',
        'setTableName' => 'Name of table in current dit',
    ];

    private $ditName   = null;
    private $tableName = null;

    public function fire()
    {
        if (! ($dit = ucfirst($this->ditName))) {
            $this->fails('Missing dit name.');
        }

        $class = underline2camelcase($dit);

        if (($_class = nsOf('dbvc', $class)) && class_exists($_class)) {
            $this->fails(
                "Dit for create dit `{$this->ditName}` already exists."
            );
        }

        if (!($tpl = pathOf('core', 'tpl/Dit.dit'))
            || !file_exists($tpl)
        ) {
            excp('Framework error: template `Dit.dit` not exists.');
        }

        $_dit  = $this->tableName ?? $dit;
        $__dit = preg_replace_callback_array([
                '/__DIT_CLASS_NAME__/u' => function ($match) use ($class) {
                    return $class;
                },
                '/__NAME__/u' => function ($match) use ($_dit) {
                    return strtolower($_dit);
                }
            ],
            file_get_contents($tpl)
        );

        file_put_contents(pathOf('dbvc', "{$class}.php"), $__dit)
        ? $this->success('New dit: '.$_class)
        : $this->fails('Add dit failed.');
    }

    public function setTableName(string $name = null)
    {
        $this->tableName = $name;
    }

    public function setDitName(string $name = null)
    {
        $this->ditName = $name;
    }
}
