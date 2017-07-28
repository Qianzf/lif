<?php

// -------------------------
//     Helpful Functions
// -------------------------

if (!function_exists('lif')) {
    function lif()
    {
        return (object)[
            'name'     => 'LiF',
            'version'  => '0.0.1',
        ];
    }
}

if (!function_exists('dd')) {
    function dd()
    {
        if (0 < func_num_args()) {
            $args = func_get_args();
            $func = extension_loaded('xdebug')
            ? 'var_dump' : 'print_r';
            foreach ($args as $arg) {
                (is_array($arg) || is_object($arg))
                ? call_user_func($func, $arg)
                : var_dump($arg);
            }
        }
        exit;
    }
}

if (!function_exists('pr')) {
    function pr()
    {
        if (0 < func_num_args()) {
            $args = func_get_args();
            $func = extension_loaded('xdebug')
            ? 'var_dump' : 'print_r';
            call_user_func_array($func, $args);
        }
    }
}

if (!function_exists('env')) {
    function env()
    {
    }
}

if (!function_exists('pathOf')) {
    function pathOf($of = null)
    {
        $root  = realpath(__DIR__.'/../../../');
        $paths = [
            'root'   => $root.'/',
            'app'    => $root.'/app/',
            'web'    => $root.'/web/',
            'view'   => $root.'/share/views/',
            'log'    => $root.'/share/logs/',
            'cache'  => $root.'/share/cache/',
            'config' => $root.'/config/',
            'static' => $root.'/web/static/',
        ];

        return is_null($of) ? $paths : (
            isset($paths[$of]) ? $paths[$of] : null
        );
    }
}

if (!function_exists('jsonEncode')) {
    function jsonEncode($arr)
    {
        return json_encode(
            $arr,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}

if (!function_exists('response')) {
    function response(
        $dat = [],
        $msg = 'ok',
        $err = 0,
        $format = 'json'
    ) {
        if ('json' === $format) {
            header('Content-type:application/json; charset=UTF-8');
            exit(jsonEncode([
                'err' => $err,
                'msg' => $msg,
                'dat' => (array) $dat,
            ]));
        }
    }
}

if (!function_exists('exception')) {
    function exception(&$exObj)
    {
        header('Content-type:application/json; charset=UTF-8');
        exit(jsonEncode([
            'Exception' => [
                'Info'  => $exObj->getMessage(),
                'Code'  => $exObj->getCode(),
                'File'  => $exObj->getFile(),
                'Line'  => $exObj->getLine(),
                'Trace' => $exObj->getTrace(),
            ],
        ]));
    }
}

if (!function_exists('error')) {
    function error($err, $msg)
    {
        response([], $msg, $err);
    }
}

if (!function_exists('array_stringify_main')) {
    function array_stringify_main($arr, &$level)
    {
        $str = '';
        $margin = str_repeat("\t", $level++);
        foreach ($arr as $key => $val) {
            $str .= $margin."'".$key."' => ";
            if (is_array($val)) {
                $str .= "[\n";
                $str .= array_stringify_main($val, $level);
                $str .= $margin."],\n";
                --$level;
            } else {
                $str .= "'".$val."',\n";
            }
        }
        return $str;
    }
}

if (!function_exists('array_stringify')) {
    function array_stringify($arr)
    {
        $level = 1;
        $str   = "[\n";
        $str  .= array_stringify_main($arr, $level);
        $str  .= ']';

        return $str;
    }
}

if (!function_exists('array_update_by_coherent_keys')) {
    function array_update_by_coherent_keys(
        $coherentKeyStr,
        $dimensionArray,
        $data
    ) {
        $coherentKeys = explode('.', $coherentKeyStr);

        return array_update_by_coherent_keys_main(
            $coherentKeys,
            $dimensionArray,
            $data
        );
    }
}

if (!function_exists('array_update_by_coherent_keys_main')) {
    function array_update_by_coherent_keys_main(
        $coherentKeys,
        $dimensionArray,
        $data
    ) {
        $tmpKeys = $coherentKeys;
        foreach ($coherentKeys as $idx => $key) {
            if (isset($dimensionArray[$key]) &&
                (false !== next($coherentKeys)) &&
                is_array($dimensionArray[$key])
            ) {
                unset($tmpKeys[$idx]);
                $dimensionArray[$key] = array_update_by_coherent_keys_main(
                    $tmpKeys,
                    $dimensionArray[$key],
                    $data
                );
                // !!! must break the loop
                // !!! or `else` will be wrong executed
                break;
            } else {
                $dimensionArray[$key] = $data;
            }
        }

        return $dimensionArray;
    }
}

if (!function_exists('cfg')) {
    function cfg($name, $keyStr, $data)
    {
        if (!$name || !is_string($name) ||
            !$keyStr || !is_string($keyStr) ||
            !$data
        ) {
            throw new \Lif\Core\Excp\API('Missing config params');
        }

        $cfgFile = pathOf('config').$name.'.php';
        $config  = array_update_by_coherent_keys(
            $keyStr,
            config($name),
            $data
        );
        $cfg  = array_stringify($config);
        $_cfg = <<< CFG
<?php

return {$cfg};\n
CFG;
        return file_put_contents($cfgFile, $_cfg, LOCK_EX);
    }
}

if (!function_exists('config')) {
    function config($name)
    {
        if (isset($GLOBALS['LIF_CFG']) &&
            isset($GLOBALS['LIF_CFG'][$name]) &&
            $GLOBALS['LIF_CFG'][$name]
        ) {
            return $GLOBALS['LIF_CFG'][$name];
        }

        $cfgFile = pathOf('config').$name.'.php';
        if (!file_exists($cfgFile)) {
            throw new \Lif\Core\Excp\API(
                'Configure File '.$cfgFile.' not exists'
            );
        }

        $cfg = include_once $cfgFile;
        $GLOBALS['LIF_CFG'][$name] = $cfg;
        return $cfg;
    }
}
