<?php

// --------------------------------------
//     Basic Helper Functions for LiF
// --------------------------------------

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
        $ver = $left = intval(file_get_contents($path));
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
        $debugNonProd = !('production' == app_env()) && app_debug();

        $display_startup_errors = $debugNonProd ? 1 : 0;
        $display_errors  = $debugNonProd ? 'On' : 'Off';
        $error_reporting = $debugNonProd ? E_ALL : 0;
        error_reporting($error_reporting);
        ini_set('display_errors', $display_errors);
        ini_set('display_startup_errors', $display_startup_errors);

        $timezone = conf('app')['timezone'] ?? 'UTC';
        date_default_timezone_set($timezone);
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mb_language('uni');
    }
}
if (!function_exists('dd')) {
    function dd(...$args)
    {
        if (0 < func_num_args()) {
            // $args = func_get_args();    // compatible with PHP < 5.6
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
    function pr(...$args)
    {
        if (0 < func_num_args()) {
            // $args = func_get_args();    // compatible with PHP < 5.6
            $func = extension_loaded('xdebug')
            ? 'var_dump' : 'print_r';

            call_user_func_array($func, $args);
        }
    }
}
if (!function_exists('ee')) {
    function ee(...$scalars)
    {
        foreach ($scalars as $value) {
            if (is_scalar($value)) {
                echo $value, PHP_EOL;
            } else {
                print_r($value);
            }
        }
        exit;
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
            return (1===count($idxes)) ? $var[$_idx] : true;
        } elseif (is_callable($var) || ($var instanceof \Closure)) {
            return $idx ? false : ($var ?? false);
        } elseif (is_object($var) && $idx) {
            $attrs = is_array($idx) ? $idx : [$idx];
            foreach ($attrs as $attr) {
                if (!isset($var->$attr) || !$var->$attr) {
                    return false;
                }
            }
            return (1===count($attrs)) ? $var->$attr : true;
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
                'core' => '\Lif\Core\\',
                'web'  => '\Lif\Core\Web\\',
                'storage'  => '\Lif\Core\storage\\',
                'strategy' => '\Lif\Core\strategy\\',
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
            'core'   => $root.'/app/core/',
            'aux'    => $root.'/app/core/aux/',
            'ctl'    => $root.'/app/ctl/',
            'mdl'    => $root.'/app/mdl/',
            'traits' => $root.'/app/traits/',
            'mock'   => $root.'/app/dat/mock/',
            'mock'   => $root.'/app/dat/mock/',
            'sysmsg' => $root.'/app/dat/msg/',
            'job'    => $root.'/app/job/',
            'view'   => $root.'/app/view/',
            'route'  => $root.'/app/route/',
            'excp'   => $root.'/app/excp/',
            'conf'   => $root.'/app/conf/',
            'mdwr'   => $root.'/app/mdwr/',
            'log'    => $root.'/var/log/',
            'cache'  => $root.'/var/cache/',
            'upload' => $root.'/var/upload/',
            'web'    => $root.'/web/',
            'static' => $root.'/web/assets/',
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
        if (! headers_sent()) {
            ob_start();
            ob_end_clean();
            mb_http_output('UTF-8');
            header('Content-type: application/json; charset=UTF-8');
        }
        exit(_json_encode($data));
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
            ('cli' === context())
            ? exit(_json_encode($info))
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

        if (! $name) {
            return conf_all($cfgPath);
        }

        if (isset($GLOBALS['LIF_CFG']) &&
            isset($GLOBALS['LIF_CFG'][$name]) &&
            $GLOBALS['LIF_CFG'][$name]
        ) {
            return $GLOBALS['LIF_CFG'][$name];
        }

        $cfgFile = $cfgPath.$name.'.php';
        if (! file_exists($cfgFile)) {
            excp('Configure File '.$cfgFile.' not exists');
        }

        $cfg = include $cfgFile;
        $GLOBALS['LIF_CFG'][$name] = $cfg;

        return $cfg;
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
if (!function_exists('build_pdo_dsn')) {
    // !!! $$conn => must `validate_db_conn` first
    function build_pdo_dsn($conn)
    {
        $dsn = $conn['driver'].':';

        switch ($conn['driver']) {
            case 'mysql':
                $dsn .= 'host='
                .$conn['host'];
                $dsn .= exists($conn, 'charset')
                ? ';charset='.$conn['charset'] : '';
                $dsn .= exists($conn, 'dbname')
                ? ';dbname='.$conn['dbname'] : '';
                break;
            case 'sqlite':
                if (exists($conn, 'memory')) {
                    $dsn .= ':memory:';
                } else {
                    $dsn .= $path = pathOf('root').$conn['path'];
                    if (!file_exists($path)) {
                        excp(
                            'Missing sqlite source file.'
                        );
                    }
                }
                break;
            default:
                excp(
                    'Missing database driver name.'
                );
                break;
        }

        return $dsn;
    }
}
if (!function_exists('validate_db_conn')) {
    function validate_db_conn(&$conn)
    {
        $driverConfMap = [
            'mysql'  => [
                'host',
                'user',
                'passwd',
            ],
            'sqlite' => [
                'path'   => 'path',
                'memory' => 'memory',
            ],
        ];
        if (!($conn['driver'] = strtolower(exists($conn, 'driver')))) {
            excp(
                'Missing database driver name.'
            );
        }
        if (!exists($driverConfMap, $conn['driver'])) {
            excp(
                'Database driver `'.$conn['driver'].'` not supported yet.'
            );
        }
        if ('sqlite' == $conn['driver']) {
            $unset = exists($conn, 'memory') ? 'path' : 'memory';
            unset($driverConfMap['sqlite'][$unset]);

            $conn['user'] = (
                exists($conn, 'user')
                && is_string($conn['user'])
            ) ? $conn['user'] : null;

            $conn['passwd'] = (
                exists($conn, 'passwd')
                && is_string($conn['passwd'])
            ) ? $conn['passwd'] : null;
        }
        if (!exists($conn, $driverConfMap[$conn['driver']])) {
            excp(
                'Missing necessary configurations for `'
                .$conn['driver']
                .'` type connection `'
                .$conn['name'].'`'
            );
        }

        return $conn;
    }
}
if (!function_exists('create_ldo')) {
    function create_ldo($conn)
    {
        $dsn  = build_pdo_dsn(validate_db_conn($conn));
        return (
            new \Lif\Core\Storage\LDO(
                $dsn,
                $conn['user'],
                $conn['passwd']
            )
        )
        ->__conn($conn['name'])
        ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
if (!function_exists('class_name')) {
    function class_name($obj)
    {
        if (!is_object($obj)) {
            return false;
        }

        return (new \ReflectionClass(get_class($obj)))->getShortName();
    }
}
if (!function_exists('class_attrs')) {
    function class_attrs($obj)
    {
        if (!is_object($obj)) {
            return false;
        }

        return (new \ReflectionClass(get_class($obj)))->getProperties();
    }
}
if (!function_exists('collect')) {
    // Convert array to a collection class
    function collect($params)
    {
        if (! is_array($params)) {
            excp('Collect target must be an array.');
        }

        return new \Lif\Core\Coll($params);
    }
}
if (! function_exists('fe')) {
    function fe($name)
    {
        return function_exists($name);
    }
}
if (! fe('view')) {
    function view(string $template, array $data = [], $cache = false)
    {
        return (
            new \Lif\Core\View($template, $data, $cache)
        )->output();
    }
}
if (! fe('sys_msg')) {
    function sys_msg($key, $lang = 'zh')
    {

    }
}
if (! fe('uuid')) {
    // Generate inner system unique number
    // $id
    // $type
    // $domain: 00 => master
    function uuid(
        $id = 0,
        $type = '01',
        $domain = '00'
    ): string
    {
        $domain  = str_pad(($domain%42), 2, '0', STR_PAD_LEFT);
        $id      = str_pad(($id%1024), 4, '0', STR_PAD_LEFT);
        $type    = in_array($type, ['01', '02', '03']) ? $type : '00';
        $postfix = mb_substr(microtime(), 2, 6);

        return date('YmdHis').$domain.$type.$id.mt_rand(1000, 9999).$postfix;
    }
}
if (! fe('sysmsg')) {
    function sysmsg($key, $lang = null)
    {
        if (! $lang) {
            $lang = $_REQUEST['lang'] ?? 'zh';
        }

        if (isset($GLOBALS['__sys_msg'])
            && is_array($GLOBALS['__sys_msg'])
            && $GLOBALS['__sys_msg']
        ) {
            $msg = $GLOBALS['__sys_msg'];
        } else {
            $msg = [];
            $path = pathOf('sysmsg').$lang;
            if (file_exists($path)) {
                $fsi = new \FilesystemIterator($path);
                foreach ($fsi as $file) {
                    if ($file->isFile() && 'php' == $file->getExtension()) {
                        $_msg = include $file->getPathname();
                        if ($_msg && is_array($_msg)) {
                            $msg = array_merge($_msg, $msg);
                        }
                    }
                }
                
                $GLOBALS['__sys_msg'] = $msg;
            }
        }

        return $msg[$key]
        ?? (
            ('zh' == $lang)
            ? '服务繁忙，请稍后再试'
            : 'Service is busy or temporarily unavailable.'
        );
    }
}
if (! fe('xml2arr')) {
    function xml2arr(string $xml) {
        return json_decode(json_encode(simplexml_load_string(
            $xml,
            'SimpleXMLElement',
            LIBXML_NOCDATA
        )), true);
    }
}
if (! fe('arr2xml')) {
    function arr2xml(array $array) {
        // Exchange keys and values of array coz: 
        // <https://stackoverflow.com/questions/1397036/how-to-convert-array-to-simplexml>
        $array = array_flip($array);
        $xml   = new \SimpleXMLElement('<xml/>');

        array_walk_recursive($array, [$xml, 'addChild']);

        // Filter line break
        return preg_replace('/(\n)*/u', '', $xml->asXML());
    }
}
