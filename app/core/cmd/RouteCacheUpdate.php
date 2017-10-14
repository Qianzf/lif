<?php

namespace Lif\Core\Cmd;

class RouteCacheUpdate extends CMD
{
    protected $intro = 'Generate/Update web route cache';

    public function fire()
    {
        $path    = pathOf('cache').'route/';
        $route   = $path.'routes.json';
        $aliases = $path.'aliases.json';

        $unlink = false;

        $msg = $unlink
        ? 'Route cache has been generated.'
        : 'Route cache has been updated.';

        $this->success($msg);
    }
}
