<?php

namespace Lif\Core\Abst;

use \Lif\Core\Intf\CMD;

abstract class Command implements CMD
{
    protected $name   = '';    // Command full name
    protected $desc   = '';    // Command desc
    protected $option = [];    // Command Options
    protected $_desc  = [];    // Options desc

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

    public function usage() : string
    {
        return segstr(
            color('Usage: ', 'LIGHT_PURPLE')
            .color('command [options] [arguments]', 'BROWN')
        );
    }

    public function options() : string
    {
        return segstr(color('Options: ', 'LIGHT_PURPLE'))
        .$this->getColoredOptionsText(
            array_group_key_by_value($this->option, ', ')
        );
    }

    public function getColoredOptionsText(array $options) : string
    {
        $text = '';
        foreach ($options as $key => $option) {
            $desc = isset($this->_desc[$key])
            ? $this->_desc[$key]
            : color('No description for this option', 'LIGHT_GRAY');

            $text .= color($option, 'BROWN')."\t".$desc;

            if (false !== next($options)) {
                $text .= line_wrap();
            }
        }

        return segstr($text);
    }
}
