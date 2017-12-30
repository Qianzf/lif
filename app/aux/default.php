<?php

// -------------------------------------
//     User defined Helper Functions
// -------------------------------------

if (! fe('ldtdf')) {
    function ldtdf(string $subtitlekey = null) {
        $sitename = ($appname = config('app.name'))
        ? $appname.L('TFMS')
        : L('LDTDFMS');

        $subtitle = $subtitlekey ? L($subtitlekey).' - ' : '';

        return $subtitle.$sitename;
    }
}
