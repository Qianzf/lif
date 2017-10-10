<?php

// ------------------------------------------
//     Helper Functions for Cli Scenarios
// ------------------------------------------

if (! fe('output')) {
    function output($params) : void {
        $output = '';
        if ($params) {
            if (iteratable($params)) {
                foreach ($params as $key => $val) {
                    if (is_string($key)) {
                        $output .= color($key, $val);
                    } elseif (is_integer($key)) {
                        $output .= $val;
                    } else {
                        excp('Illegal output content.');
                    }
                }
            } elseif (is_scalar($params)) {
                $output = $params;
            } else {
                excp('Illegal output contents.');
            }
        }

        echo $output, line_wrap();
        exit;
    }
}
if (! fe('console')) {
    function console() {
        return (
            isset($GLOBALS['LIF_CONSOLE'])
            && is_object($GLOBALS['LIF_CONSOLE'])
        ) ? $GLOBALS['LIF_CONSOLE']
        : (
            new \Lif\Core\Cli\Console
        );
    }
}
if (! fe('color')) {
    function color(string $text, string $color) : string {
        return ('cli' == context())
        ? console()->render($text, $color)
        : $text;
    }
}
if (! fe('segstr')) {
    function segstr(string $string) : string {
        return line_wrap().$string.line_wrap();
    }
}
if (! fe('format_cmd')) {
    // !!! Case-sensitive
    function format_cmd(string $cmd) : string {
        $arr = explode('_', $cmd);

        array_walk($arr, function (&$item, $key) {
            $item = ucfirst($item);
        });

        return implode('', $arr);
    }
}
if (! fe('get_core_cmd_class')) {
    function get_core_cmd_class(string $cmd, string $act) : string {
        return nsOf('_cmd')
        .format_cmd($cmd)
        .format_cmd($act);
    }
}
if (! fe('is_cmd_option')) {
    function is_cmd_option(string $option) : bool {
        return preg_match('/^--?\w*$/u', $option);
    }
}
if (! fe('cli_excp_output')) {
    function cli_excp($excp) {
        return output(segstr(
            color('Command execution error!', 'LIGHT_RED')
            .line_wrap(2)
            .color($excp->getMessage(), 'RED')
        ));
    }
}
