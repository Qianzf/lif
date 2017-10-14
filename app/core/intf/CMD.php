<?php

// ---------------------------------
//     LiF command line contract
// ---------------------------------

namespace Lif\Core\Intf;

interface CMD
{
    // Execute main logic
    public function fire(? array $params);
    
    // Parase out command's options and arguments
    public function parse(
        array $params,
        array &$option = [],
        array &$argv = []
    ) : void;

    // Help message of current command
    public function help();

    // Usage message of current command
    public function usage() : string;

    // Options message of current command
    public function options() : string;

    // Options desc messages of current command
    public function optionAndDesc() : string;

    // Intro messages of current command
    public function withIntro(bool $string = false);

    // Execute option's actions of current command
    public function withOptions(array $options);

    // Get colored options text string for console outputing
    public function getColoredOptionsText(array $options) : string;
}
