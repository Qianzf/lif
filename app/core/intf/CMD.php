<?php

// ---------------------------------
//     LiF command line contract
// ---------------------------------

namespace Lif\Core\Intf;

interface CMD
{
    // Execute main logic
    public function fire();

    // Help message of current command
    public function help($output = null);

    // Usage message of current command
    public function usage(?string $val, ?string $option);

    // Options message of current command
    public function options(?string $val, ?string $option);

    // Intro messages of current command
    public function withIntro(bool $string = false);

    // Set and Execute option's actions of current command
    public function withOptions(array $options) : CMD;

    // Set unknown command parameters from CLI
    public function setUnknown(array $unknowns) : CMD;

    // Get colored options text string for console outputing
    public function getColoredOptionsText(array $options) : string;

    // Output success meassage
    public function success(string $msg, bool $exit = true) : void;
    
    // Output failure meassage
    public function fails(string $msg, bool $exit = true) : void;

    // Output information meassage
    public function info(string $msg, bool $exit = true) : void;
}
