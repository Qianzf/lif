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
            $this->info('Web application is running, nothing happend.');
        }

        if (unlink($lock)) {
            $this->success('Web Application started successfully.');
        }

        $this->fails('Starting web application failed.');
    }
}
