<?php

namespace Lif\Core\Cmd\App;

use Lif\Core\Abst\Command;

class Stop extends Command
{
    protected $intro = 'Stop servering web application.';

    public function fire()
    {
        $lock = pathOf('app', '.lock');

        if (file_exists($lock)) {
            $this->info('Application was stopped, nothing happened.');
        }

        if (touch($lock)) {
            $this->success('Application is stopped.');
        }

        $this->fails('Stopping application failed.');
    }
}
