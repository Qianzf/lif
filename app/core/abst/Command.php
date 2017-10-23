<?php

// ------------------------------
//     LiF base command class
// ------------------------------

namespace Lif\Core\Abst;

use \Lif\Core\Intf\CMD;

abstract class Command implements CMD
{
    use \Lif\Core\Traits\MethodNotExists;
    
    protected $name    = '';    // Command full name
    protected $intro   = '';    // Command desc
    protected $option  = [];    // Command Options
    protected $desc    = [];    // Command options desc
    // Global options for every command to be executed
    protected $_option = [
        '-H'        => 'help',
        '--help'    => 'help',
        '-U'        => 'usage',
        '--usage'   => 'usage',
        '-O'        => 'options',
        '--options' => 'options',
        '-D'        => 'debug',
        '--debug'   => 'debug',
        '-S'        => 'silence',
        '--silence' => 'silence',
        '-v'        => 'verbose',    // Level 1
        '-vv'       => 'verbose',    // Level 2
        '-vvv'      => 'verbose',    // Level 3 <=> equals to `--debug`
        '--verbose' => 'verbose',    // Level 3
        '--json'       => 'json',
        '--json-file'  => 'json',
        '--xml'        => 'xml',
        '--xml-file'   => 'xml',
        '--query'      => 'query',
        '--query-file' => 'query',
    ];
    // Global Options desc
    protected $_desc   = [
        'help'    => 'Output help message for current command',
        'usage'   => 'Output usage message for current command',
        'options' => 'Output options message for current command',
        'debug'   => 'Output detials of command execution',
        'verbose' => 'How many detials of command execution will be outputed',
        'silence' => 'Execute command and no output',
        'json'  => 'Passing command parameters via JSON string or JSON file',
        'xml'   => 'Passing command parameters via XML string or JSON file',
        'query' => 'Passing command parameters via query string or query file',
    ];

    protected $optionAll = [];    // All command options
    protected $descAll   = [];    // All command option's desc
    protected $params    = [];    // Command parameters from CLI
    protected $unknowns  = [];    // Unknown command parameters from CLI
    protected $options   = [];    // Options from CLI

    public function fire() {}

    public function setOptions(array $options) : CMD
    {
        $this->options = $options;

        return $this;
    }

    public function setUnknown(array $unknowns) : CMD
    {
        $this->unknowns = $unknowns;

        return $this;
    }

    protected function json(string $json, string $option = 'json') : CMD
    {
        if ('--json' == $option) {
            $this->params = _json_decode($json, true) ?? [];
        } else {
            if (! file_exists($json)) {
                excp('JSON file not exists: '.$json);
            }

            $this->params = _json_decode(file_get_contents($json), true) ?? [];
        }

        return $this;
    }

    protected function xml(string $xml, string $option = 'xml') : CMD
    {
        if ('--xml' == $option) {
            $this->params = xml2arr($xml) ?? [];
        } else {
            if (! file_exists($xml)) {
                excp('XML file not exists: '.$xml);
            }

            $this->params = xml2arr(file_get_contents($xml)) ?? [];
        }

        return $this;
    }

    protected function query(string $query, string $option = 'query') : CMD
    {
        if ('--query' == $option) {
            parse_str($query, $this->params);
        } else {
            if (! file_exists($query)) {
                excp('Query string file not exists: '.$query);
            }

            parse_str(file_get_contents($query), $this->params);
        }

        return $this;
    }

    public function help($output = null)
    {
        return output(
            $this->usage()
            .$this->options()
        );
    }

    // !!! Only working when executed first before other code
    public function debug() : void
    {
        error_reporting('E_ALL');
        ini_set('display_startup_errors', 'On');
        ini_set('display_errors', 'On');
    }

    // !!! Only working when executed first before other code
    public function silence() : void
    {
        error_reporting(0);
        ini_set('display_startup_errors', 'Off');
        ini_set('display_errors', 'Off');
    }

    // !!! Only working when executed first before other code
    public function verbose(?string $val, string $level) : void
    {
        switch ($level) {
            case '-v':
                error_reporting('E_ERROR');
                break;
            case '-vv':
                error_reporting('E_ALL & ~E_NOTICE)');
                break;
            case '-vvv':
            case '--verbose':
            default:
                $this->debug();
                break;
        }
    }

    public function usage(string $val = null, string $option = null)
    {
        $usage = segstr(color('Usage: ', 'LIGHT_PURPLE')
            .linewrap()
            .tab_indent()
            .color($this->name().' [options] [arguments]', 'BROWN')
        );

        return $option ? output($usage) : $usage;
    }

    public function optionAll()
    {
        if (! $this->optionAll) {
            // If same option both in gloab and current
            // Then use current instead
            $this->optionAll = array_merge(
                $this->_option,
                $this->option
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

    public function withOptions(array $options) : CMD
    {
        $this->setOptions($options);

        $_options = $this->optionAll();
        foreach ($options as $option => $value) {
            if (! in_array($option, array_keys($_options))) {
                excp('Option not exists: '.$option);
            } elseif (! method_exists($this, $_options[$option])) {
                excp('Option handler not exists: '.$_options[$option]);
            }

            call_user_func_array([
                $this,
                $_options[$option]
            ], [$value, $option]);
        }

        return $this;
    }

    protected function name() : string
    {
        if (! $this->name) {
            $this->name = class2cmd($this);
        }

        return $this->name;
    }

    protected function intro() : string
    {
        if (! $this->intro) {
            $this->intro = 'No introduction for this command';
        }

        return $this->intro;
    }

    public function withIntro(bool $string = false)
    {
        return $string
        ? segstr(
            color($this->name(), 'LIGHT_BLUE')
            .tab_indent()
            .$this->intro()
        )
        : [
            'name'  => $this->name(),
            'intro' => $this->intro(),
        ];
    }

    public function options(string $val = null, string $option = null)
    {
        $options = segstr(color('Options: ', 'LIGHT_PURPLE'))
        .$this->getColoredOptionsText(
            array_group_key_by_value($this->optionAll(), ', ')
        );

        return $option ? output($options) : $options;
    }

    public function getColoredOptionsText(array $options) : string
    {
        $text    = '';
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

    public function success(string $msg) : void
    {
        output(segstr(color($msg, 'GREEN')));
    }

    public function fails(string $msg) : void
    {
        output(segstr(color($msg, 'RED')));
    }
}
