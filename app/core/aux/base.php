<?php

// -------------------------
//     Helpful Functions
// -------------------------

if (!function_exists('lif')) {
    function lif()
    {
        response([
            'Name'    => 'LiF',
            'version' => '0.0.1',
        ], 'Hello World.');
    }
}

if (!function_exists('init')) {
    function init()
    {
        $timezone = config('app')['timezone'] ?? 'UTC';
        date_default_timezone_set($timezone);
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mb_language('uni');
        if ('production' != app_env()) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        }
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
            exit;
        }
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

if (!function_exists('format_route_key')) {
    function format_route_key($route)
    {
        $routeKey = implode('_', array_filter(explode('/', $route)));
        return $routeKey ? $routeKey : '_';
    }
}

if (!function_exists('app_debug')) {
    function app_debug()
    {
        $app = config('app');
        return (isset($app['debug']) && in_array($app['debug'], [
            true,
            false
        ])) ? $app['debug'] : true;
    }
}

if (!function_exists('app_env')) {
    function app_env()
    {
        $app = config('app');
        return (isset($app['env']) && in_array($app['env'], [
            'local',
            'staging',
            'production',
        ])) ? $app['env'] : 'local';
    }
}

if (!function_exists('context')) {
    function context()
    {
        return ('cli' === php_sapi_name())
        ? 'cli' : 'web';
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
            'route'  => $root.'/app/route/',
            'config' => $root.'/config/',
            'static' => $root.'/web/static/',
        ];

        return is_null($of) ? $paths : (
            isset($paths[$of]) ? $paths[$of] : null
        );
    }
}

if (!function_exists('_json_encode')) {
    function _json_encode($arr)
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
            json_http_response(_json_encode([
                'err' => $err,
                'msg' => $msg,
                'dat' => (array) $dat,
            ]));
        }
    }
}

if (!function_exists('json_http_response')) {
    function json_http_response($data)
    {
        header('Content-type:application/json; charset=UTF-8');
        exit($data);
    }
}

if (!function_exists('exception')) {
    function exception(&$exObj)
    {
        $info = [
            'Exception' => $exObj->getMessage(),
            'Code'      => $exObj->getCode(),
        ];

        if (('production' != app_env()) && app_debug()) {
            $info['File']  = $exObj->getFile();
            $info['Line']  = $exObj->getLine();
            $info['Trace'] = $exObj->getTrace();
        }

        json_http_response(_json_encode($info));
    }
}

if (!function_exists('api_exception')) {
    function api_exception($msg, $err = 500)
    {
        throw new \Lif\Core\Excp\API($msg, $err);
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

if (!function_exists('config_all')) {
    function config_all($cfgPath = null)
    {
        $cfgPath = $cfgPath ?? pathOf('config');

        foreach (scandir($cfgPath) as $cfg) {
            $path = $cfgPath.$cfg;
            if (is_file($path)) {
                $file = pathinfo($path);
                if ('php' == $file['extension']) {
                    $GLOBALS['LIF_CFG'][$file['filename']] = config(
                        $file['filename'],
                        $cfgPath
                    );
                }
            }
        }

        return $GLOBALS['LIF_CFG'] ?? [];
    }
}

if (!function_exists('config')) {
    function config($name = null, $cfgPath = null)
    {
        $cfgPath = $cfgPath ?? pathOf('config');

        if (!$name) {
            return config_all($cfgPath);
        }

        if (isset($GLOBALS['LIF_CFG']) &&
            isset($GLOBALS['LIF_CFG'][$name]) &&
            $GLOBALS['LIF_CFG'][$name]
        ) {
            return $GLOBALS['LIF_CFG'][$name];
        }

        $cfgFile = $cfgPath.$name.'.php';
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
