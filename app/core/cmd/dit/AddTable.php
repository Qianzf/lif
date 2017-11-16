<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class AddTable extends Command
{
    protected $intro = 'Create a database table';

    protected $option = [
        '-N'     => 'setTableName',
        '--name' => 'setTableName',
    ];
    protected $desc = [
        'setTableName' => 'Name of table to be created',
    ];

    private $tableName = null;

    public function fire()
    {
        if (! ($table = ucfirst($this->tableName))) {
            $this->fails('Missing table name.');
        }

        $class = 'Create'.underline2camelcase($table).'Table';

        if (($_class = nsOf('dbvc', $class)) && class_exists($_class)) {
            $this->fails(
                "Dit for create table `{$this->tableName}` already exists."
            );
        }

        if (!($tpl = pathOf('core', 'tpl/CreateTable.dit'))
            || !file_exists($tpl)
        ) {
            excp('Framework error: template `CreateTable.dit` not exists.');
        }

        $dit = preg_replace_callback_array([
                '/__DIT_CLASS_NAME__/u' => function ($match) use ($class) {
                    return $class;
                },
                '/__NAME__/u' => function ($match) use ($table) {
                    return strtolower($table);
                }
            ],
            file_get_contents($tpl)
        );

        file_put_contents(pathOf('dbvc', "{$class}.php"), $dit)
        ? $this->success('New dit: '.$_class)
        : $this->fails('Add dit failed.');
    }

    public function setTableName(string $name = null)
    {
        $this->tableName = $name;
    }
}
