<?php

namespace Lif\Core\Abst;

abstract class Logger implements \Lif\Core\Intf\Logger
{
    private $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function isLegalConfig() : bool
    {
    }
}
