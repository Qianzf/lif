<?php

namespace Lif\Core\Cmd\App;

use Lif\Core\Abst\Command;

class Start extends Command
{
    protected $intro = 'Start servering web application';

    public function fire()
    {
        $lock = pathOf('app', '.lock');

        if (! file_exists($lock)) {
            $this->info('Application is running, nothing happened.');
        }

        if (unlink($lock)) {
            $this->success('Application started successfully.');
        }

        $this->fails('Starting application failed.');
    }
}
