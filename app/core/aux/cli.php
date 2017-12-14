<?php

// ------------------------------------------
//     Helper Functions for Cli Scenarios
// ------------------------------------------

if (! fe('output')) {
    function output($params, int $err = 0) : void {
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

        exit($err);
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
if (! fe('classname2ns')) {
    function classname2ns(string $classname) : string {
        return decode_classname($classname, '\\', 'ucfirst');
    }
}
if (! fe('cmdclassname')) {
    function cmdclassname($object) {
        if (! is_object($object)) {
            excp('Illegal command object');
        }
        $ns = get_class($object);
        if (($nsCore = 'Lif\Core\Cmd')
            && (false !== mb_strpos($ns, $nsCore))
        ) {
            $_ns = str_replace($nsCore, '', $ns);
        } elseif (($nsUser = 'Lif\Cmd')
            && (false !== mb_strpos($ns, $nsUser))
        ) {
            $_ns = str_replace($nsUser, '', $ns);
        } else {
            excp('Illegal command class: '.$ns);
        }

        return ns2classname($_ns);
    }
}
if (! fe('class2cmd')) {
    function class2cmd($object) : string {
        return decode_classname(
            cmdclassname($object),
            '.',
            'strtolower'
        );
    }
}
if (! fe('cmd_split')) {
    function cmd_split(string $cmd) : array {
        $arr = explode('.', $cmd);

        array_walk($arr, function (&$item, $key) {
            $item = ucfirst(strtolower($item));
        });

        return $arr;
    }
}
if (! fe('cmd2class')) {
    function cmd2class(string $cmd) {
        return implode('', cmd_split($cmd));
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
        return preg_match('/^(--?[\w\-]*)(\=.*)?$/u', $option);
    }
}
if (! fe('cli_excp')) {
    function cli_excp($excp) {
        return output(segstr(color($excp->getMessage(), 'RED')), 1);
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
            excp('Command class not exists: '.$cmdns);
        }

        $cmd = new $cmdns;

        if (!($cmd instanceof \Lif\Core\Abst\Command)
            || !method_exists($cmd, 'withIntro')
        ) {
            excp('Illegal command class: '.$cmdns);
        }

        $withIntro = $cmd->withIntro($string);

        unset($cmd);

        return $withIntro;
    }
}
if (!fe ('get_cmds_main')) {
    function get_cmds_main(
        string $path,
        string $ns,
        bool $string,
        string &$str = '',
        array &$cmds = []
    ) {
        $escapeFiles = [
            'Command.php',
            'CMD.php',
        ];
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
               } elseif ($file->isDir()) {
                    $dirname = $file->getBasename();
                    $_path   = $path.$dirname;
                    $_ns     = $ns.ucfirst($dirname).'\\';
                    get_cmds_main($_path, $_ns, $string, $str, $cmds);
               }
            }
            unset($fsi);
        }
    }
}
if (! fe('get_cmds')) {
    function get_cmds(
        string $path,
        string $ns,
        bool $string = false
    ) {
        $cmds = [];
        $str  = '';

        get_cmds_main($path, $ns, $string, $str, $cmds);

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
            .tabdent()
            .$intro
            .linewrap();
        }

        return $text;
    }
}
if (! fe('get_core_cmds')) {
    function get_core_cmds(bool $string = false) {
        $coreCmds = get_cmds(pathOf('_cmd'), nsOf('_cmd'));

        return $string ? get_cmds_text($coreCmds) : $coreCmds;
    }
}
if (! fe('get_user_cmds')) {
    function get_user_cmds(bool $string = false) {
        $userCmds = get_cmds(pathOf('cmd'), nsOf('cmd'));

        return $string ? get_cmds_text($userCmds) : $userCmds;
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
if (! fe('try_cmd_classes')) {
    function try_cmd_classes(array $cmds) {
        $keys  = array_keys($cmds);
        unset($keys[0]);
        $keys  = array_values($keys);
        $_keys = subsets($keys);

        if (false !== ($ns = cmd_classns(implode('', $cmds)))) {
            return $ns;
        }

        foreach ($_keys as $_key) {
            $tmp = $cmds;
            foreach ($_key as $key) {
                if (! is_string($cmds[$key])) {
                    excp('Illegal partition array.');
                }
                $tmp[$key] = '\\'.$cmds[$key];
                if (false !== ($ns = cmd_classns(implode('', $tmp)))) {
                    return $ns;
                }
            }
        }

        return false;
    }
}
if (! fe('if_cmd_exists')) {
    function if_cmd_exists($cmd) {
        if (is_object($cmd)
            && ($cmd instanceof \Lif\Core\Abst\Command)
        ) {
            $cmds = classname_split(cmdclassname($cmd));
        } elseif (is_string($cmd)) {
            $cmds = cmd_split($cmd);
        } else {
            excp('Illegal command');
        }

        return try_cmd_classes($cmds);
    }
}
if (! fe('interval')) {
    function interval(\Closure $callback, int $secs) {
        // check if timer is running
        $callback();
    }
}
if (! fe('timeout')) {
    function timeout(\Closure $callback, int $secs) {
        // check if timer is running
        $callback();
    }
}
