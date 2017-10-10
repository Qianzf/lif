<?php

namespace Lif\Core\Intf;

interface CMD
{
    public function fire(array $params);
    
    public function parse(
        array $params,
        array &$option = [],
        array &$argv = []
    ) : void;

    public function help();
    public function usage() : string;
    public function options() : string;
    public function getColoredOptionsText(array $options) : string;
}
