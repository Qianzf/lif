<?php

// -------------------------------------
//     Basic Helper Functions for LiF
// -------------------------------------

if (!function_exists('lif')) {
    function lif()
    {
        $msg = 'Hello World';
        $lif = [
            'name'    => 'LiF',
            'version' => get_lif_ver(),
        ];

        ('cli' === context())
        ? exit(_json_encode(array_merge([
            'msg' => $msg,
        ], $lif)))
        : response($lif, $msg);
    }
}
if (!function_exists('get_lif_ver')) {
    // --------------------------------------------
    //     The version format used in LiF:
    //     [major].[minor].[release].[build]
    //     2 commits = 1 build
    //     1 release = 16 build  = 32 commits
    //     1 minor   = 8 release = 256 commits
    //     1 major   = 4 minor   = 1024 commits
    // --------------------------------------------
    function get_lif_ver()
    {
        $path = pathOf('root').'.ver';
        if (!file_exists($path)) {
            return '0.0.0.0';
        }
        // Plus 1 here because git client hook `pre-commit` always lag 1 time
        $ver = $left = intval(file_get_contents($path)) + 1;
        $major   = floor($left / 1024);
        $left    = $ver - $major*1024;
        $minor   = floor($left / 256);
        $left    = $left - $minor*256;
        $release = floor($left / 32);
        $left    = $left - $release*32;
        $build   = floor($left / 2);

        return $major.'.'.$minor.'.'.$release.'.'.$build;
    }
}
if (!function_exists('init')) {
    function init()
    {
        $timezone = conf('app')['timezone'] ?? 'UTC';
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
if (!function_exists('app_debug')) {
    function app_debug()
    {
        $app = conf('app');
        return (isset($app['debug']) && in_array($app['debug'], [
            true,
            false
        ])) ? $app['debug'] : true;
    }
}
if (!function_exists('app_env')) {
    function app_env()
    {
        $app = conf('app');
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
if (!function_exists('exists')) {
    function exists($var, $idx = null)
    {
        // !!! be carefurl if `$var` is not an assoc array
        if (is_array($var) && $idx) {
            $idxes = is_array($idx) ? $idx : [$idx];
            foreach ($idxes as $_idx) {
                if (!isset($var[$_idx]) || !$var[$_idx]) {
                    return false;
                }
            }
            return (1===count($idxes)) ? $var[$idx] : true;
        } elseif (is_object($var) && $idx) {
            $attrs = is_array($idx) ? $idx : [$idx];
            foreach ($attrs as $attr) {
                if (!isset($var->$attr) || !$var->$attr) {
                    return false;
                }
            }
            return (1===count($attrs)) ? $var->$idx : true;
        }

        return (isset($var) && $var) ? $var : false;
    }
}
if (!function_exists('nsOf')) {
    function nsOf($of = null)
    {
        if (!$of) {
            return '\\';
        }

        if (is_object($of)) {
            $fullClassName = get_class($of);
            $nsArr = explode('\\', $fullClassName);
            unset($nsArr[count($nsArr)-1]);
            $ns = implode('\\', $nsArr);

            return $ns;
        }

        if (is_string($of)) {
            $nsArr = [
                'ctl'  => '\Lif\Ctl\\',
                'mdl'  => '\Lif\Mdl\\',
                'mdwr' => '\Lif\Mdwr\\',
            ];
            return $nsArr[$of] ?? '\\';
        }
    }
}
if (!function_exists('pathOf')) {
    function pathOf($of = null)
    {
        $root  = realpath(__DIR__.'/../../../');
        $paths = [
            'root'   => $root.'/',
            'app'    => $root.'/app/',
            'aux'    => $root.'/app/core/aux/',
            'web'    => $root.'/web/',
            'view'   => $root.'/app/view/',
            'log'    => $root.'/var/logs/',
            'cache'  => $root.'/var/cache/',
            'route'  => $root.'/app/route/',
            'conf'   => $root.'/app/conf/',
            'mdwr'   => $root.'/app/mdwr/',
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
if (!function_exists('json_http_response')) {
    function json_http_response($data)
    {
        if (!headers_sent()) {
            mb_http_output('UTF-8');
            header('Content-type:application/json; charset=UTF-8');
        }
        exit($data);
    }
}
if (!function_exists('exception')) {
    // ----------------------------------------------------------------------
    //     Errors caused by behaviours inside framework called exceptions
    //     eg: route bind illegal, file not exists, etc.
    // ----------------------------------------------------------------------
    //     Exceptions is used for developer to locate bugs
    //     Debug model and environment will effect exception output
    // ----------------------------------------------------------------------
    function exception(&$exObj, $format = 'json')
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

        if ('json' === $format) {
            $info = _json_encode($info);

            ('cli' === context())
            ? exit($info)
            : json_http_response($info);
        }
    }
}
if (!function_exists('excp')) {
    function excp($msg, $err = 500, $format = 'json')
    {
        throw new \Lif\Core\Excp\Lif($msg, $err, $format);
    }
}
if (!function_exists('format_namespace')) {
    function format_namespace($namespaceRaw)
    {
        if (is_array($namespaceRaw) && $namespaceRaw) {
            return implode(
                '\\',
                array_filter(
                    explode('\\', implode('\\', $namespaceRaw))
                )
            );
        }
        if (is_string($namespaceRaw) && $namespaceRaw) {
            return implode('\\', array_filter(explode('\\', $namespaceRaw)));
        }

        return '\\';
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
            throw new \Lif\Core\Excp\Lif('Missing config params');
        }

        $cfgFile = pathOf('conf').$name.'.php';
        $config  = array_update_by_coherent_keys(
            $keyStr,
            conf($name),
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
if (!function_exists('conf_all')) {
    function conf_all($cfgPath = null)
    {
        $cfgPath = $cfgPath ?? pathOf('conf');

        foreach (scandir($cfgPath) as $cfg) {
            $path = $cfgPath.$cfg;
            if (is_file($path)) {
                $file = pathinfo($path);
                if ('php' == $file['extension']) {
                    $GLOBALS['LIF_CFG'][$file['filename']] = conf(
                        $file['filename'],
                        $cfgPath
                    );
                }
            }
        }

        return $GLOBALS['LIF_CFG'] ?? [];
    }
}
if (!function_exists('conf')) {
    function conf($name = null, $cfgPath = null)
    {
        $cfgPath = $cfgPath ?? pathOf('conf');

        if (!$name) {
            return conf_all($cfgPath);
        }

        if (isset($GLOBALS['LIF_CFG']) &&
            isset($GLOBALS['LIF_CFG'][$name]) &&
            $GLOBALS['LIF_CFG'][$name]
        ) {
            return $GLOBALS['LIF_CFG'][$name];
        }

        $cfgFile = $cfgPath.$name.'.php';
        if (!file_exists($cfgFile)) {
            excp('Configure File '.$cfgFile.' not exists');
        }

        $cfg = include_once $cfgFile;
        $GLOBALS['LIF_CFG'][$name] = $cfg;
        return $cfg;
    }
}
if (!function_exists('build_pdo_dsn')) {
    function build_pdo_dsn($conn)
    {
        $dsn = $conn['driver']
        .':host='
        .$conn['host'];

        $dsn .= exists($conn, 'charset')
        ? ';charset='.$conn['charset'] : '';

        $dsn .= exists($conn, 'dbname')
        ? ';dbname='.$conn['dbname'] : '';

        return $dsn;
    }
}
if (!function_exists('db')) {
    function db($conn = null)
    {
        return \Lif\Core\Factory\Storage::fetch('db', 'pdo', $conn);
    }
}
if (!function_exists('db_conns')) {
    function db_conns($conn = null)
    {
        return \Lif\Core\Factory\Storage::fetch('db', 'conns', $conn);
    }
}
