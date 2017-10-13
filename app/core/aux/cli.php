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
if (! fe('classname2ns')) {
    function classname2ns(string $classname) : string {
        return decode_classname($classname, '\\', 'ucfirst');
    }
}
if (! fe('class2cmd')) {
    // !!! Case-sensitive
    function class2cmd(string $classname, string $implode = '.') : string {
        return decode_classname($classname, $implode, 'strtolower');
    }
}
if (! fe('cmd2class')) {
    function cmd2class(string $cmd) {
        $arr = explode('.', $cmd);

        array_walk($arr, function (&$item, $key) {
            $item = ucfirst(strtolower($item));
        });

        return implode('', $arr);
    }
}
if (! fe('user_cmd_class')) {
    function usercmd_class(string $cmd) : string {
        return nsOf('cmd').cmd2class($cmd);
    }
}
if (! fe('core_cmd_class')) {
    function corecmd_class(string $cmd) : string {
        return nsOf('_cmd').cmd2class($cmd);
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

        if (! ($class = exists($arr, 0))
            || !is_string($class)
        ) {
            excp('Illegal filename.');
        }

        return ucfirst($class);
    }
}
if (! fe('cmdintro')) {
    function cmdintro(string $cmdns, bool $string = false) {
        if (! class_exists($cmdns)) {
            excp('Command class not exists.');
        }

        $cmd = new $cmdns;

        if (!($cmd instanceof \Lif\Core\Abst\Command)
            || !method_exists($cmd, 'withIntro')
        ) {
            excp('Illegal command class.');
        }

        $withIntro = $cmd->withIntro($string);

        unset($cmd);

        return $withIntro;
    }
}
if (! fe('get_cmds')) {
    function get_cmds(
        string $path,
        string $ns,
        bool $string = false
    ) {
        $escapeFiles = [
            'Command.php',
        ];

        $cmds = [];
        $str  = '';

        if (file_exists($path)) {
            $fsi = new \FilesystemIterator($path);
            foreach ($fsi as $file) {
                if ($file->isFile()
                    && ('php' == $file->getExtension())
                    && ($fname = $file->getBasename())
                    && !in_array($fname, $escapeFiles)
                ) {
                    $cmd = cmdintro($ns.fname2cname($fname), $string);

                    $string
                    ? ($str .= $cmd)
                    : ($cmds[$cmd['name']] = $cmd['intro']);
               }
            }
            unset($fsi);
        }

        return $string ? $str : $cmds;
    }
}
if (! fe('get_cmds_text')) {
    function get_cmds_text(array $cmds) : string {
        $text = '';
        $tmp  = [];
        ksort($cmds);
        foreach ($cmds as $name => $intro) {
            $arr = explode('.', $name);
            if (! ($cate = exists($arr, 0))) {
                excp('Illegal command name: '.$name);
            }
            if (! isset($tmp[$cate])) {
                $tmp[$cate] = true;
                $text .= segstr(color(ucfirst($cate), 'LIGHT_GREEN')
                    .linewrap()
                );
            }

            $text .= space_indent()
            .color($name, 'GREEN')
            .tab_indent()
            .$intro
            .linewrap();
        }

        return $text;
    }
}
if (! fe('get_all_cmds')) {
    function get_all_cmds(bool $string = false) {
        $coreCmds = get_cmds(pathOf('_cmd'), nsOf('_cmd'));
        $userCmds = get_cmds(pathOf('cmd'), nsOf('cmd'));

        $cmds = array_merge($coreCmds, $userCmds);
        return $string ? get_cmds_text($cmds) : $cmds;
    }
}
if (! fe('cmd_classns')) {
    function cmd_classns(string $classname) {
        $_ns = nsOf('_cmd').$classname;
        $ns  = nsOf('cmd').$classname;
        $_nsExists = class_exists($_ns);
        $nsExists  = class_exists($ns);
        if ($_nsExists && $nsExists) {
            excp(
                'Command class `'.$classname.'` already exists in: '.$_ns
            );
        } elseif (!$_nsExists && !$nsExists) {
            return false;
        }

        return $_nsExists ? $_ns : $ns;
    }
}
if (! fe('if_cmd_exists')) {
    function if_cmd_exists(string $classname) {
        if (false === ($firstTry = cmd_classns($classname))) {
            if (false === ($secondTry = cmd_classns(
                cmd2class($classname)
            ))) {
                return false;
            }

            return $secondTry;
        }

        return $firstTry;
    }
}
