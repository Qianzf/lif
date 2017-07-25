<?php

require_once __DIR__.'/../vendor/autoload.php';

$_LIF_CONFIG = require_once __DIR__.'/cfg.php';

(new Lif\Core\Application($_LIF_CONFIG))->handle();
