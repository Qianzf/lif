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
        if (! $this->ctlName) {
            $this->fails('No name specified for controller');
        }
        $this->success('Created new controller: '.$this->ctlName);
    }

    protected function setCtlName(string $name = null)
    {
        $this->ctlName = $name;
    }
}
