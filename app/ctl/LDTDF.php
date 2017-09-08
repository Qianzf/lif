<?php

namespace Lif\Ctl;

class LDTDF extends Ctl
{
    public function index()
    {
        $nameWitchRole = 'cjli';
        $languages = [
            'zh' => '简体中文',
            'en' => 'English',
        ];

        $sysLang = $_REQUEST['lang'] ?? 'zh';

        view('index', compact('nameWitchRole', 'languages', 'sysLang'), false);
    }
}
