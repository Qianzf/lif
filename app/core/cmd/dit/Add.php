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
        '--type'  => 'setDitType',
        '--file'  => 'setSeedFile',
        '-F'      => 'setSeedFile',
    ];
    protected $desc = [
        'setDitName'   => 'Name of dit to be created',
        'setDitType'   => 'Type of dit to be created: dbvc, dbseed',
        'setTableName' => 'Name of table in current dit',
        'setSeedFile'  => 'Filename of dit seed',
    ];

    private $ditType   = 'dbvc';
    private $ditName   = null;
    private $tableName = null;
    private $seedFile  = null;

    public function fire()
    {
        if (! ($dit = ucfirst($this->ditName))) {
            $this->fails('Missing dit name.');
        }

        $class = underline2camelcase($dit);

        if (($_class = nsOf($this->ditType, $class))
            && class_exists($_class)
        ) {
            $this->fails(
                "Dit for create dit `{$this->ditName}` already exists."
            );
        }

        if (!($tpl = pathOf('core', "tpl/Dit.{$this->ditType}"))
            || !file_exists($tpl)
        ) {
            excp("
                Framework error: template `Dit.{$this->ditType}` not exists.
            ");
        }

        $table = $this->tableName ?? $dit;
        $seed  = $this->seedFile  ?? $dit;
        $__dit = preg_replace_callback_array([
            '/__DIT_CLASS_NAME__/u' => function ($match) use ($class) {
                return $class;
            },
            '/__TABLE__/u' => function ($match) use ($table) {
                return strtolower($table);
            },
            '/__SEED__/u' => function ($match) use ($seed) {
                return camelcase2underline($seed);
            },
        ],
            file_get_contents($tpl)
        );

        file_put_contents(pathOf($this->ditType, "{$class}.php"), $__dit)
        ? $this->success('New dit: '.$_class)
        : $this->fails('Add dit failed.');
    }

    public function setTableName(string $name = null)
    {
        $this->tableName = $name;
    }

    public function setDitType(string $type = null)
    {
        $this->ditType = strtolower($type ?? 'dbvc');
    }

    public function setSeedFile(string $name = null)
    {
        $this->seedFile = $name;
    }

    public function setDitName(string $name = null)
    {
        $this->ditName = $name;
    }
}
