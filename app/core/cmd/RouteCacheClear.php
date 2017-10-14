<?php

namespace Lif\Core\Cmd;

class RouteCacheClear extends CMD
{
    protected $intro = 'Clear web route cache';

    public function fire()
    {
        $path    = pathOf('cache').'route/';
        $route   = $path.'routes.json';
        $aliases = $path.'aliases.json';

        $unlink = false;
        if (file_exists($route)) {
            $unlink = true;
            @unlink($route);
        }
        if (file_exists($aliases)) {
            $unlink = true;
            @unlink($aliases);
        }

        $msg = $unlink
        ? 'Route cache has been cleared.'
        : 'No route cache, nothing happened.';

        $this->success($msg);
    }
}
