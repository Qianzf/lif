<?php

namespace Lif\Ctl;

class LDTDF extends Ctl
{
    protected $shareData = [];

    public function __construct()
    {
        $nameWitchRole = 'cjli';
        $languages = [
            'zh' => '简体中文',
            'en' => 'English',
        ];

        $sysLang = $_REQUEST['lang'] ?? 'zh';

        $this->shareData = compact(
            'nameWitchRole',
            'languages',
            'sysLang'
        );
    }

    public function index()
    {
        view('ldtdf/index',
            $this->shareData
        );
    }

    public function profile()
    {
        view('ldtdf.profile', 
            $this->shareData
        );
    }

    public function logout()
    {
    }
}
