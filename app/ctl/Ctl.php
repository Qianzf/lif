<?php

namespace Lif\Ctl;

use Lif\Core\Ctl as CtlBase;

class Ctl extends CtlBase
{
    public function __construct()
    {
        parent::__construct();

        share('languages', [
            'zh' => '简体中文',
            'en' => 'English',
        ]);
    }
}
