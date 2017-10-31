<?php

// -----------------------------------------------------------
//     LiF log contract (mostly follws the PSR-3 standard)
//     See: <http://www.php-fig.org/psr/psr-3/>
// -----------------------------------------------------------

namespace Lif\Core\Intf;

interface Logger
{
    // Detailed debug information
    public function debug($log, array $context = []) : void;

    // Interesting events
    // Examples:
    // - User logs in
    // - SQL logs
    public function info($log, array $context = []) : void;
    
    // Normal but significant events
    public function notice($log, array $context = []) : void;
    
    // Exceptional occurrences that are not errors
    // Examples:
    // - Use of deprecated APIs
    // - Poor use of an API
    // - Undesirable things that are not necessarily wrong
    // - So forth ...
    public function warning($log, array $context = []) : void;
    
    // Runtime errors that do not require immediate action
    // but should typically be logged and monitored.
    public function error($log, array $context = []) : void;
    
    // Critical conditions
    public function critical($log, array $context = []) : void;
    
    // Action must be taken immediately
    public function alert($log, array $context = []) : void;
    
    // System is unusable
    public function emergency($log, array $context = []) : void;

    // Calling this method must have the same result
    // as calling the level-specifc method
    // And throw an `InvalidLogArgument` excption
    // when $level is not exist in definition
    public function log($level = 'info', $log, array $context = []) : void;
}
