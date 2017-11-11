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
            $this->info('Web application is stopped, nothing happend.');
        }

        if (touch($lock)) {
            $this->success('Web Application is stopped.');
        }

        $this->fails('Stopping web application failed.');
    }
}
