<?php

namespace Lif\Core\Cmd;

class ViewCacheClear extends CMD
{
    protected $intro = 'Clear web view template cache';

    public function fire()
    {
        $path    = pathOf('cache').'view/';

        $unlink = false;

        $msg = $unlink
        ? 'Route cache has been cleared.'
        : 'No view cache, nothing happened.';

        $this->success($msg);
    }
}
