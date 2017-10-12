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

        echo $output, linewrap();
        exit;
    }
}
if (! fe('console')) {
    function console() {
        $class = '\Lif\Core\Cli\Console';
        return (
            isset($GLOBALS['LIF_CLI_CONSOLE'])
            && ($GLOBALS['LIF_CLI_CONSOLE'] instanceof $class)
        ) ? $GLOBALS['LIF_CLI_CONSOLE']
        : (
            new $class
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
        return linewrap().$string.linewrap();
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
            .linewrap(2)
            .color($excp->getMessage(), 'RED')
        ));
    }
}
if (! fe('fname2cname')) {
    function fname2cname(string $filename) {
        $arr = explode('.', $filename);

        if (! ($class = exists($arr, 0))) {
            excp('Illegal filename.');
        }

        return $class;
    }
}
if (! fe('get_cmd_attrs')) {
    function get_cmd_attrs(string $cmdns) {
        if (! class_exists($cmdns)) {
            excp('Command class not exists.');
        }

        $cmd = new $cmdns;

        if (!method_exists($cmd, 'cmdAndIntro')
        || !method_exists($cmd, 'optionAndDesc')) {
            excp('Illegal command class.');
        }
    }
}
if (! fe('get_cmds')) {
    function get_cmds(string $path, string $ns) : array {
        $escapeFiles = [
            'Command.php',
        ];

        if (file_exists($path)) {
            $fsi = new \FilesystemIterator($path);
            foreach ($fsi as $file) {
                if ($file->isFile()
                    && ('php' == $file->getExtension())
                    && ($fname = $file->getBasename())
                    && !in_array($fname, $escapeFiles)
                ) {
                    $cmd = get_cmd_attrs($ns.fname2cname($fname));
                    // array_group_key_by_value($cmd, ', ');
               }
            }
            unset($fsi);
        }
        return [];
    }
}
if (! fe('get_all_cmds')) {
    function get_all_cmds() : array {
        $userCmds = get_cmds(pathOf('cmd'), nsOf('cmd'));
        $coreCmds = get_cmds(pathOf('_cmd'), nsOf('_cmd'));

        return [];
    }
}
