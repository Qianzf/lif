<?php

namespace Lif\Core\Intf;

interface Mailer
{
    public function send(array $config, array $params) : bool;
}
