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

    private $modelName = null;

    public function fire()
    {   
        if (! $this->modelName) {
            $this->fails('No name specified for model');
        }
        $this->success('Created new model: '.$this->modelName);
    }

    protected function setModelName(string $name = null)
    {
        $this->modelName = $name;
    }
}
