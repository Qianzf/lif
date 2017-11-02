<?php

namespace Lif\Core\Abst;

use Lif\Core\Excp\InvalidLogArgument;

abstract class Logger implements \Lif\Core\Intf\Logger
{
    protected $config = [];
    protected $data   = [
        'tdt' => null,
        'tzn' => 'UTC',
        'tsp' => null,
        'dat' => null,
        'lvl' => 'log',
    ];

    const LOG_LEVEL = [
        1 => 'debug',
        2 => 'info',
        3 => 'notice',
        4 => 'warning',
        5 => 'error',
        6 => 'critical',
        7 => 'alert',
        8 => 'emergency',
        9 => 'log',    // LiF added method
    ];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    // Specific logger must rewrite this method
    public function validate() : bool
    {
        // Validate logger config
        return false;
    }

    // Specific logger must rewrite this method
    public function write()
    {
        // Write data into log driver
    }

    public function prepare($log, array $context = [], string $level = 'log')
    {
        if (true === $this->validate()) {
            $this->data = build_log_str(
                str_with_context($log, $context),
                $level
            );

            $this->write();
        }
    }

    public function debug($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'debug');
    }

    public function info($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'info');
    }

    public function notice($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'notice');
    }

    public function warning($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'warning');
    }

    public function error($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'error');   
    }

    public function critical($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'critical');
    }

    public function alert($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'alert');
    }

    public function emergency($log, array $context = []) : void
    {
        $this->prepare($log, $context, 'emergency');
    }

    public function log(
        $level,
        $log,
        array $context = []
    ) : void {
        if (!isset(self::LOG_LEVEL[$level])
            && !in_array($level, self::LOG_LEVEL)
        ) {
            exception(new InvalidLogArgument($level));
        }

        $this->prepare($log, $context, $level);
    }
}
