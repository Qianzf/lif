<?php

namespace Lif\Core\Cli;

class Console
{
    protected $colors = [
        'BLACK'  => '0;30',
        'BLUE'   => '0;34',
        'GREEN'  => '0;32',
        'CYAN'   => '0;36',
        'RED'    => '0;31',
        'PURPLE' => '0;35',
        'BROWN'  => '0;33',
        'YELLOW' => '1;33',
        'WHITE'  => '1;37',
        'LIGHT_GRAY'   => '0;37',
        'DARK_GRAY'    => '1;30',
        'LIGHT_BLUE'   => '1;34',
        'LIGHT_GREEN'  => '1;32',
        'LIGHT_CYAN'   => '1;36',
        'LIGHT_RED'    => '1;31',
        'LIGHT_PURPLE' => '1;35',
    ];

    public function render(string $text, string $color) : string
    {
        if (! isset($this->colors[$color])) {
            excp('Console color not found: '.$color);
        }

        $_color = $this->colors[$color];

        return "\033[{$_color}m{$text}\033[0m";
    }
}
