<?php

// ---------------------------------
//     LiF basic command class
// ---------------------------------

namespace Lif\Core\Abst;

use \Lif\Core\Intf\CMD;

abstract class Command implements CMD
{
    use \Lif\Core\Traits\MethodNotExists;
    
    protected $name    = '';    // Command full name
    protected $intro   = '';    // Command desc
    protected $option  = [];    // Command Options
    protected $desc    = '';    // Command options desc
    // Global options for every command to be executed
    protected $_option = [
        '-H'        => 'help',
        '--help'    => 'help',
        '-D'        => 'debug',
        '--debug'   => 'debug',
        '-v'        => 'verbose',    // Level 1
        '-vv'       => 'verbose',    // Level 2
        '-vvv'      => 'verbose',    // Level 3
        '--verbose' => 'verbose',    // Level 3
    ];
    // Global Options desc
    protected $_desc   = [
        'help'    => 'Output help message for current command',
        'debug'   => 'Output detials of command execution',
        'verbose' => 'How many detials of command execution will be outputed',
    ];
    protected $optionAll = [];    // All command options
    protected $descAll   = [];    // All command option's desc

    public function __construct()
    {
    }

    public function fire(array $params)
    {
    }

    public function parse(
        array $params,
        array &$options = null,
        array &$args = null
    ) : void {
        array_walk($params, function ($item) use (&$options, &$args) {
            if (is_string($item)) {
                if (is_cmd_option($item)) {
                    $options = to_arr($options, $item);
                } else {
                    $args   = to_arr($args, $item);
                }
            }
        });
    }

    public function help()
    {
        return output(
            $this->usage()
            .$this->options()
        );
    }

    public function debug()
    {

    }

    public function verbose(string $level)
    {
        switch ($level) {
            case '-v':
                # code...
                break;
            case '-vv':
                # code...
                break;
            case '-vvv':
                # code...
                break;
            default:
                # code...
                break;
        }
    }

    public function usage() : string
    {
        return segstr(
            color('Usage: ', 'LIGHT_PURPLE')
            .linewrap()
            .tab_indent()
            .color('command [options] [arguments]', 'BROWN')
        );
    }

    public function optionAll()
    {
        if (! $this->optionAll) {
            // If same option both in gloab and current
            // Then use current instead
            $this->optionAll = array_merge(
                $this->option,
                $this->_option
            );
        }

        return $this->optionAll;
    }

    public function descAll()
    {
        if (! $this->descAll) {
            // If same option desc both in gloab and current
            // Then use current instead
            $this->descAll = array_merge(
                $this->desc,
                $this->_desc
            );
        }

        return $this->descAll;
    }

    public function withOptions(array $options)
    {
        $_options = $this->optionAll();
        foreach ($options as $option) {
            if (! in_array($option, array_keys($_options))) {
                excp('Option not exists: '.$option);
            } elseif (! method_exists($this, $_options[$option])) {
                excp('Option handler not exists: '.$_options[$option]);
            }

            call_user_func_array([
                $this,
                $_options[$option]
            ], [$option]);
        }
    }

    protected function name() : string
    {
        if (! $this->name) {
            $this->name = class_name($this);
        }

        return $this->name;
    }

    public function optionAndDesc() : string
    {
    }

    public function cmdAndIntro() : string
    {
        return segstr(
            color($this->name())
        );
    }

    public function options() : string
    {
        return segstr(color('Options: ', 'LIGHT_PURPLE'))
        .$this->getColoredOptionsText(
            array_group_key_by_value($this->optionAll(), ', ')
        );
    }

    public function getColoredOptionsText(array $options) : string
    {
        $text = '';
        $descAll = $this->descAll();
        foreach ($options as $key => $option) {
            $desc = isset($descAll[$key])
            ? $descAll[$key]
            : color('No description for this option', 'LIGHT_GRAY');

            $text .= tab_indent().color($option, 'BROWN').tab_indent().$desc;

            if (false !== next($options)) {
                $text .= linewrap();
            }
        }

        return segstr($text);
    }
}
