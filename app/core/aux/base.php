<?php

// --------------------------------------
//     Basic Helper Functions for LiF
// --------------------------------------

if (! function_exists('fe')) {
    function fe($name) {
        return function_exists($name);
    }
}
if (! fe('lif')) {
    function lif() {
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
if (! fe('get_lif_ver')) {
    // --------------------------------------------
    //     The version format used in LiF:
    //     [major].[minor].[release].[build]
    //     2 commits = 1 build
    //     1 release = 16 build  = 32 commits
    //     1 minor   = 8 release = 256 commits
    //     1 major   = 4 minor   = 1024 commits
    // --------------------------------------------
    function get_lif_ver() {
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
if (! fe('init')) {
    function init() {
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
        ini_set('session.name', 'LIFSESSID');
    }
}
if (! fe('dd')) {
    function dd(...$args) {
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
if (! fe('pr')) {
    function pr(...$args) {
        if (0 < func_num_args()) {
            // $args = func_get_args();    // compatible with PHP < 5.6
            $func = extension_loaded('xdebug')
            ? 'var_dump' : 'print_r';

            call_user_func_array($func, $args);
        }
    }
}
if (! fe('ee')) {
    function ee(...$scalars) {
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
if (! fe('app_debug')) {
    function app_debug() {
        if (! file_exists(pathOf('conf').'app.php')) {
            return true;
        }

        $app = conf('app');
        return (isset($app['debug']) && in_array($app['debug'], [
            true,
            false
       ])) ? $app['debug'] : true;
    }
}
if (! fe('app_env')) {
    function app_env() {
        if (! file_exists(pathOf('conf').'app.php')) {
            return 'local';
        }

        $app = conf('app');
        return (isset($app['env']) && in_array($app['env'], [
            'local',
            'staging',
            'production',
       ])) ? $app['env'] : 'local';
    }
}
if (! fe('context')) {
    function context() {
        return ('cli' === php_sapi_name())
        ? 'cli' : 'web';
    }
}
if (! fe('legal_or')) {
    function legal_or(array &$data, array $rulesWithDefaults) {
        if (! $rulesWithDefaults) {
            return true;
        } elseif (!is_array($data) && !is_object($data)) {
            excp('Validate source must be an arra or object.');
        }

        $validator = new \Lif\Core\Validation;
        foreach ($rulesWithDefaults as $key => list($rules, $default)) {
            if (!isset($data[$key]) || is_null($data[$key])) {
                $data[$key] = $default;
            } else {
                if (! is_array($rules)) {
                    $rules = [$key => $rules];
                }
                
                if (true !== $validator->run($data, $rules)) {
                    $data[$key] = $default;
                }
            }
        }
    }
}
if (! fe('exists')) {
    // !!! Be careful to check bool value like false
    function exists($var, $idx = null) {
        // !!! Be carefurl if `$var` is not an assoc array
        if (is_array($var) && !is_null($idx)) {
            $idxes = is_array($idx) ? $idx : [$idx];
            foreach ($idxes as $_idx) {
                if (! isset($var[$_idx])) {
                    return false;
                }
            }
            return (1 === count($idxes)) ? $var[$_idx] : true;
        } elseif (is_callable($var) || ($var instanceof \Closure)) {
            return $idx ? false : ($var ?? false);
        } elseif (is_object($var) && $idx) {
            $attrs = is_array($idx) ? $idx : [$idx];
            foreach ($attrs as $attr) {
                if (! isset($var->$attr)) {
                    return false;
                }
            }
            return (1===count($attrs)) ? $var->$attr : true;
        }

        return $var;
    }
}
if (! fe('nsOf')) {
    function nsOf($of = null) {
        if (! $of) {
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
                '_cmd' => '\Lif\Core\Cmd\\',
                'cmd'  => '\Lif\Cmd\\',
                'lib'  => '\Lif\Core\Lib\\',
                'storage'  => '\Lif\Core\storage\\',
                'strategy' => '\Lif\Core\strategy\\',
           ];
            return $nsArr[$of] ?? '\\';
        }
    }
}
if (! fe('pathOf')) {
    function pathOf($of = null) {
        $root  = realpath(__DIR__.'/../../../');
        $paths = [
            'root'   => $root.'/',
            'app'    => $root.'/app/',
            'core'   => $root.'/app/core/',
            'aux'    => $root.'/app/core/aux/',
            '_cmd'   => $root.'/app/core/cmd/',
            'cmd'    => $root.'/app/cmd/',
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
if (! fe('_json_decode')) {
    function _json_decode(string $json) {
        $res = is_json($json);

        if (is_integer($res)) {
            excp(get_json_err_msg($res));
        }

        return $res;
    }
}
if (! fe('_json_encode')) {
    function _json_encode($arr) {
        return json_encode(
            $arr,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
       );
    }
}
if (! fe('xml_http_response')) {
    function xml_http_response($data) {
        if (! headers_sent()) {
            ob_start();
            ob_end_clean();
            mb_http_output('UTF-8');
            header('Content-type: application/xml; charset=UTF-8');
        }
        
        echo arr2xml($data);
        exit;
    }
}
if (! fe('json_http_response')) {
    function json_http_response($data) {
        if (! headers_sent()) {
            ob_start();
            ob_end_clean();
            mb_http_output('UTF-8');
            header('Content-type: application/json; charset=UTF-8');
        }
        
        echo _json_encode($data);
        exit;
    }
}
if (! fe('exception')) {
    // ----------------------------------------------------------------------
    //     Errors caused by behaviours inside framework called exceptions
    //     eg: route bind illegal, file not exists, etc.
    // ----------------------------------------------------------------------
    //     Exceptions is used for developer to locate bugs
    //     Debug model and environment will effect exception output
    // ----------------------------------------------------------------------
    function exception($exObj, $format = 'json') {
        if ('cli' === context()) {
            return cli_excp($exObj);
        }

        $response = $format.'_http_response';
        if (! function_exists($response)) {
            $response = 'json_http_response';
        }

        $info  = [
            'Exception' => $exObj->getMessage(),
            'Code'      => $exObj->getCode(),
       ];

        // !!! Make sure check app conf path first
        // !!! Or infinite loop will occur when app conf file not exists
        if (('production' != app_env()) && app_debug()) {
            $trace         = $exObj->getTrace();
            $info['File']  = $trace[0]['file'];
            $info['Line']  = $trace[0]['line'];
            unset($trace[0]);
            $info['Trace'] = $trace;
        }

        $GLOBALS['LIF_EXCP'] = true;
        
        return $response($info);
    }
}
if (! fe('excp')) {
    function excp($msg, $err = 500, $format = 'json') {
        throw new \Lif\Core\Excp\Lif($msg, $err, $format);
    }
}
if (! fe('format_namespace')) {
    function format_namespace($namespaceRaw) {
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
if (! fe('subsets')) {
    // See: <https://stackoverflow.com/questions/6092781/finding-the-subsets-of-an-array-in-php>
    function subsets(array $data, int $minLen = 1) : array {
        $count   = count($data);
        $times   = pow(2, $count);
        $result  = [];
        for ($i = 0; $i < $times; ++$i) {
            // $bin = sprintf('%0'.$count.'b', $i);
            $tmp = [];
            for ($j = 0; $j < $count; ++$j) {
                // Use bitwise operation is more faster than sprintf
                if ($i >> $j & 1) {
                // if ('1' == $bin{$j}) {    // get NO.$j letter in string $bin
                    $tmp[] = $data[$j];
                }
            }
            if (count($tmp) >= $minLen) {
                $result[] = $tmp;
            }
        }

        return $result; 
    }
}
if (! fe('array_partition')) {
    function array_partition(array $arr, string $by = '\\') : array {
        $keys   = array_keys($arr);
        unset($keys[0]);
        $keys   = array_values($keys);
        $_keys  = subsets($keys);
        $_arr[] = implode('', $arr);    // Or: array_unshift($_keys, []);

        foreach ($_keys as $_key) {
            $tmp = $arr;
            foreach ($_key as $key) {
                if (! is_string($arr[$key])) {
                    excp('Illegal partition array.');
                }
                $tmp[$key] = $by.$arr[$key];
            }

            $_arr[] = implode('', $tmp);
        }

        return $_arr;
    }
}
if (! fe('array_stringify')) {
    function array_stringify($arr) {
        $level = 1;
        $str   = "[\n";
        $str  .= array_stringify_main($arr, $level);
        $str  .= ']';

        return $str;
    }
}
if (! fe('array_stringify_main')) {
    function array_stringify_main($arr, &$level) {
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
if (! fe('array_query_by_coherent_keys')) {
    function array_query_by_coherent_keys(array $haystack, string $key) {
        if (!$key || false === mb_strpos($key, '.')) {
            return $haystack[$key] ?? null;
        }

        $coherentKeys = explode('.', $key);

        $query  = null;
        $tmpArr = $haystack;

        foreach ($coherentKeys as $val) {
            $query = ($tmpArr = ($tmpArr[$val] ?? null));
        }

        return $query;
    }
}
if (! fe('array_update_by_coherent_keys')) {
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
if (! fe('array_update_by_coherent_keys_main')) {
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
if (! fe('array_group_key_by_value')) {
    function array_group_key_by_value(
        array $arr,
        string $implode = null
   ) : array {
        $tmp = $_tmp = [];
        foreach ($arr as $key => $value) {
            $tmp[$value][] = $key;
            if (is_string($implode)) {
                $_tmp[$value] = implode($implode, $tmp[$value]);
            }
        }

        return is_string($implode)
        ? $_tmp : $tmp;
    }
}
if (! fe('cfg')) {
    function cfg($name, $keyStr, $data) {
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
if (! fe('conf_all')) {
    function conf_all($cfgPath = null) : array {
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
if (! fe('conf')) {
    function conf($name = null, $cfgPath = null) : array{
        $cfgPath = $cfgPath ?? pathOf('conf');

        if (! $name) {
            return conf_all($cfgPath);
        }


        if (isset($GLOBALS['LIF_CFG'])
            && isset($GLOBALS['LIF_CFG'][$name])
            && $GLOBALS['LIF_CFG'][$name]
       ) {
            return array_query_by_coherent_keys($GLOBALS['LIF_CFG'], $name);
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
if (! fe('config')) {
    function config($key) {
        return array_query_by_coherent_keys(conf_all(), $key);
    }
}
if (! fe('db')) {
    function db($conn = null) {
        return \Lif\Core\Factory\Storage::fetch('db', 'pdo', $conn);
    }
}
if (! fe('db_conns')) {
    function db_conns($conn = null) {
        return \Lif\Core\Factory\Storage::fetch('db', 'conns', $conn);
    }
}
if (! fe('build_pdo_dsn')) {
    // !!! $$conn => must `validate_db_conn` first
    function build_pdo_dsn($conn) {
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
if (! fe('validate_db_conn')) {
    function validate_db_conn(&$conn) {
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
if (! fe('create_ldo')) {
    function create_ldo($conn) {
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
if (! fe('model')) {
    function model(string $class, $pk = null) {
        if (! class_exists($class)) {
            excp('Model class not exists: '.$class);
        }

        return new $class($pk);
    }
}
if (! fe('escape_fields')) {
    function escape_fields(string $raw) : string
    {
        if (false !== mb_strpos($raw, '.')) {
            $arr = explode('.', $raw);
            array_walk($arr, function (&$item, $key) {
                $item = '`'.$item.'`';
            });

            return implode('.', $arr);
        }

        return '`'.$raw.'`';
    }
}
if (! fe('classname_split')) {
    function classname_split($classname) : array {
        if (is_object($classname)) {
            $classname = classname($classname);
        }
        if (! is_string($classname)) {
            excp('Illegal class name.');
        }

        $arr = [];
        preg_replace_callback(
            '/[A-Z][a-z]*/u',
            function ($matches) use (&$arr) {
                $arr[] = ucfirst(strtolower($matches[0]));
            },
            $classname
        );

        return $arr;
    }
}
if (! fe('decode_classname')) {
    function decode_classname(
        string $classname,
        string $implode = '.',
        string $replace = 'strtolower'
    ) : string {
        $res = preg_replace_callback(
            '/[A-Z][a-z]*/u',
            function ($matches) use ($implode, $replace) {
                if (! function_exists($replace)) {
                    excp('Function not defined: '.$replace);
                }
                return $replace($matches[0]).$implode;
        }, $classname);

        return implode($implode, array_filter(explode($implode, $res)));
    }
}
if (! fe('ns2classname')) {
    function ns2classname(string $ns) {
        return str_replace('\\', '', $ns);
    }
}
if (! fe('classname')) {
    function classname($obj) {
        if (!is_object($obj)) {
            return false;
        }

        return (new \ReflectionClass(get_class($obj)))->getShortName();
    }
}
if (! fe('classns')) {
    function classns($obj) {
        if (!is_object($obj)) {
            return false;
        }

        return (new \ReflectionClass(get_class($obj)))->getNamespaceName();
    }
}
if (! fe('class_attrs')) {
    function class_attrs($obj) {
        if (!is_object($obj)) {
            return false;
        }

        return (new \ReflectionClass(get_class($obj)))->getProperties();
    }
}
if (! fe('collect')) {
    // Convert array to a collection class
    function collect($params, $origin = null) {
        if (! is_array($params)) {
            excp('Collect target must be an array.');
        }

        return new \Lif\Core\Coll($params, $origin);
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
   ): string {
        $domain  = str_pad(($domain%42), 2, '0', STR_PAD_LEFT);
        $id      = str_pad(($id%1024), 4, '0', STR_PAD_LEFT);
        $type    = in_array($type, ['01', '02', '03']) ? $type : '00';
        $postfix = mb_substr(microtime(), 2, 6);

        return date('YmdHis').$domain.$type.$id.mt_rand(1000, 9999).$postfix;
    }
}
if (! fe('sysmsgs')) {
    function sysmsgs() {
        return (new \Lif\Core\SysMsg)->get();
    }
}
if (! fe('load')) {
    // !!! Loaded files cann't contain `$this`
    function load(string $path, string $desc = 'File', $once = true) : void {
        if (! file_exists($path)) {
            excp($desc.' does not exists.');
        }

        if ($once) {
            require_once $path;
        } else {
            require $path;
        }
    }
}
if (! fe('load_array')) {
    function load_array(string $path) : array {
        $msg = [];
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
            unset($fsi);
        }

        return $msg;
    }
}
if (! fe('sysmsg')) {
    function sysmsg($key, string $lang = null) {
        $key = strtoupper($key);
        if (! $lang || !is_string($lang)) {
            $lang = $_REQUEST['lang'] ?? null;
            $session = new \Lif\Core\Web\Session;
            if (! $lang) {
                $lang = $session->get('__lang') ?? 'zh';
            }
            $session->set('__lang', $lang);
        }

        if (isset($GLOBALS['__sys_msg'][$lang])
            && is_array($GLOBALS['__sys_msg'][$lang])
            && $GLOBALS['__sys_msg'][$lang]
       ) {
            $msg = $GLOBALS['__sys_msg'][$lang];
        } else {
            $langPath = pathOf('sysmsg');
            $path = $langPath.$lang;
            $path = file_exists($path) ? $path : $langPath.'zh';
            $GLOBALS['__sys_msg'][$lang] = $msg = load_array($path);
        }

        if (isset($msg[$key])) {
            return $msg[$key];
        }

        // Support get missing system message by single word key
        if (false !== mb_strpos($key, '_')) {
            $arr  = explode('_', $key);
            $_msg = '';
            $stub = (('en' == $lang) ? ' ' : '');
            foreach ($arr as $_key) {
                if (! isset($msg[$_key])) {
                    return $key;
                }

                $_msg .= $stub.$msg[$_key];
            }

            if ($_msg = ltrim($_msg)) {
                return $GLOBALS['__sys_msg'][$lang][$key] = $_msg;
            }
        }

        return $key;
    }
}
if (! fe('lang')) {
    function lang($key, $lang = null)
    {
        return sysmsg($key, $lang);
    }
}
if (! fe('xml2arr')) {
    function xml2arr($xml, $loaded = false) {
        libxml_use_internal_errors(true);
        $xml = $loaded
        ? $xml
        : simplexml_load_string(
            $xml,
            'SimpleXMLElement',
            LIBXML_NOCDATA
       );

        if (($error = libxml_get_last_error())
            && isset($error->message)
        ) {
            libxml_clear_errors();
            excp('Wrong XML format: '.$error->message);
        }

        return json_decode(json_encode($xml), true);
    }
}
if (! fe('xml2obj')) {
    // Return string of error message if $xml is illegal
    // Return object when $xml is legal
    function xml2obj(string $xml) {
        $res = is_xml($xml);

        return $res['data'];
    }
}
if (! fe('arrToXML')) {
    function arrToXML(array $array, string &$xml): string
    {
        foreach ($array as $key => &$val) {
            if (is_array($val)) {
                $_xml = '';
                $val = arrToXML($val, $_xml);
            }
            $xml .= "<$key>$val</$key>";
        }

        unset($val);

        return $xml;
    }
}
if (! fe('arr2xml')) {
    function arr2xml(array $array, string $xml = '') : string {
        $_xml  = '<?xml version="1.0" encoding="utf-8"?><xml>'
        .arrToXML($array, $xml)
        .'</xml>';

        return $_xml;
    }
}
if (! fe('arr2xml_unsafe')) {
    // !!! This function is dysfunctional when same values in $array
    function arr2xml_unsafe(array $array) {
        // Exchange keys and values of array coz: 
        // <https://stackoverflow.com/questions/1397036/how-to-convert-array-to-simplexml>
        $array = array_flip($array);
        $xml   = new \SimpleXMLElement('<xml/>');

        array_walk_recursive($array, [$xml, 'addChild']);

        // Filter line break
        return preg_replace('/(\n)*/u', '', $xml->asXML());
    }
}
if (! fe('xml_decode')) {
    function xml_decode(string $xml, $array = true) {
        if ($array) {
            return xml2arr($xml);
        }

        return xml2obj($xml);
    }
}
if (! fe('xml_encode')) {
    function xml_encode($data) {
        if (is_array($data)) {
            return arr2xml($data);
        } elseif (is_object($data)) {
            return method_exists($data, 'toXml')
            ? (
                is_array($data->toXml())
                ? arr2xml($ret)
                : 'Return value of `toXml()` in object.'
           )
            : 'Missing `toXml()` method in object.';
        }

        return false;
    }
}
if (! fe('request_http_api')) {
    function request_http_api(
        string $uri,
        string $type = 'GET',
        array $headers = [],
        $params = []
   ) {
        $setOpt = [
            CURLOPT_URL            => $uri,
            CURLOPT_RETURNTRANSFER => true,
       ];

        if ($headers) {
            $setOpt[CURLOPT_HTTPHEADER] = $headers;
        }

        if ('POST' == $type) {
            $setOpt[CURLOPT_POST]       = true;
            $setOpt[CURLOPT_POSTFIELDS] = $params;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $setOpt);
        $res = curl_exec($ch);

        $errNo  = curl_errno($ch);
        $errMsg = curl_error($ch);

        curl_close($ch);

        return [
            'err' => $errNo,
            'msg' => ($errMsg ?: 'ok'),
            'res' => $res,
       ];
    }
}
if (! fe('request_json_api')) {
    function request_json_api(
        $uri,
        $type = 'GET',
        $params = [],
        $headers = []
   ) {
        $headers = [
            'Content-Type: application/json; Charset=UTF-8',
       ];

        $ret = request_http_api($uri, $type, $headers, $params);

        if (0 == $ret['err'] && $ret['res']) {
            $ret['res'] = is_integer($json = is_json($ret['res']))
            ? get_json_err_msg($json) : $json;
        }

        return $ret;
    }
}
if (! fe('request_xml_api')) {
    function request_xml_api(
        $uri,
        $type = 'GET',
        $params = [],
        $headers = []
   ) {
        $headers = [
            'Content-Type: application/xml; Charset=UTF-8',
       ];

        $ret = request_http_api($uri, $type, $headers, $params);

        if (0 == $ret['err'] && $ret['res']) {
            $xml = is_xml($ret['res']);

            $ret['res'] = (true === $xml['status'])
            ? xml2arr($xml['data'], true)
            : $xml['data'];
        }

        return $ret;
    }
}
if (! fe('is_json')) {
    // Return an array or object if $json is legal
    // Return an integer number if $json is illegal
    function is_json(string $json, bool $array = true) {
        $res = json_decode($json, $array);

        return (($err = json_last_error()) == JSON_ERROR_NONE)
        ? $res : $err;
    }
}
if (! fe('get_json_err_msg')) {
    function get_json_err_msg(int $code): string {
        if (! defined('JSON_ERROR_RECURSION')) {
            define('JSON_ERROR_RECURSION', 6);
        }
        if (! defined('JSON_ERROR_INF_OR_NAN')) {
            define('JSON_ERROR_INF_OR_NAN', 7);
        }
        if (! defined('JSON_ERROR_UNSUPPORTED_TYPE')) {
            define('JSON_ERROR_UNSUPPORTED_TYPE', 8);
        }
        $knownErrors = [
            JSON_ERROR_NONE  => 'No error',
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
            JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX    => 'Syntax error',
            JSON_ERROR_UTF8      => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded',
            JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded',
            JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given',
       ];

        $errMsg = $knownErrors[$code] ?? 'Unknown error';

        return 'Illegal JSON: '.$errMsg;
    }
}
if (! fe('json_last_error_msg')) {
    function json_last_error_msg(): string {
        return get_json_err_msg(json_last_error());
    }
}
if (! fe('is_xml')) {
    // Must return an array:
    // status => bool: is XML or not
    // data   => mixed: XML doc (status=true) or error message (status=false)
    function is_xml(string $xml) : array {
        libxml_use_internal_errors(true);
        if (! ($doc = simplexml_load_string(
            $xml,
            'SimpleXMLElement',
            LIBXML_NOCDATA
       ))) {
            $error = libxml_get_last_error();    // LibXMLError object

            libxml_clear_errors();

            return [
                'status' => false,
                'data'   => 'Illegal XML: '.$error->message,
           ];
        }

        return [
            'status' => true,
            'data'   => $doc,
       ];
    }
}
if (! fe('unihex_text')) {
    // Attained like this: <https://www.branah.com/unicode-converter>
    function unihex_text(string $unicode) : string {
        $json = '{"key":"'.$unicode.'"}';

        $res  = json_decode($json);

        if ($code = json_last_error()) {
            return get_json_err_msg($code);
        }

        return $res->key;
    }
}
if (! fe('unihex2text')) {
    // Slower than unihex_text(~4x) but suitable for more complicated scenarios
    // See: <https://zh.wikipedia.org/wiki/UTF-16>
    function unihex2text(string $unicode) : string {
        return preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/u',
            function ($match) {
                return mb_convert_encoding(
                    pack(
                        'H*',
                        $match[1]
                   ),
                    'UTF-8',
                    'UTF-16BE'
               );
            },
            $unicode
       );
    }
}
if (! fe('get_func_cost')) {
    function get_func_cost(Closure $closure, int $times = 10000) : float {
        $start = microtime(true);
        for ($i=0; $i<=$times; ++$i) {
            $closure();
        }
        $end   = microtime(true);

        return $end-$start;
    }
}
if (! fe('is_timestamp')) {
    function is_timestamp($timestamp) {
        return (
            is_integer($timestamp)
            && ($timestamp >= 0)
            && ($timestamp <= 2147472000)
       );
    }
}
if (! fe('validate')) {
    function validate(array $data, array $rules) {
        return (new \Lif\Core\Validation)->run(
            $data,
            $rules
       );
    }
}
if (! fe('classname')) {
    function classname(string $raw) {
        $needle = false;
        if (false !== mb_strpos($raw, '-')) {
            $needle = '-';
        } elseif (false !== mb_strpos($raw, '.')) {
            $needle = '.';
        }

        if (false !== $needle) {
            $arr = explode($needle, $raw);

            array_walk($arr, function (&$item, $key) {
                $item = ucfirst($item);
            });

            return implode('', $arr);
        }

        return ucfirst($raw);
    }
}
if (! fe('email')) {
    // ----------------------------------------------------------------
    //     Keys of $params :
    //     - Array`to` => Support multiple receivers for same email
    //                 => Key is receiver's email
    //                 => Value is receiver's dispaly name
    //     - String `title` => Email subject
    //     - String `body`  => Email context
    // ----------------------------------------------------------------
    function email(array $params, string $sender = null) : bool {
        if (! ($config = config('mail'))
            || (true !== validate($config, [
                'default' => 'need|string',
                'senders' => 'need|array',
           ]))
       ) {
            excp('Missing mail sender configurations.');
        }

        $sender = $sender ?? $config['default'];

        if (! isset($config['senders'][$sender])
            || ! ($sender = $config['senders'][$sender])
            || ! is_array($sender)
       ) {
            excp('Missing configurations for mail sender: '.$sender);
        }

        if (true !== ($err = validate($sender, [
            'driver' => 'need|string',
            'host'   => 'need|domain',
            'port'   => 'need|int|min:25',
            'account'      => 'need|string',
            'credential'   => 'need|string',
            'sender_name'  => 'need|string',
            'sender_email' => 'need|email',
            'encryption'   => 'string|in:ssl,tls',
       ]))) {
            excp('Illegal mail sender configurations: '.$err);
        } elseif (true !== ($err = validate($params, [
            'to'    => 'need|array',
            'title' => 'need|string',
            'body'  => 'need|string',
       ]))) {
            excp('Illegal email message: '.$err);
        }

        $driver = nsOf('lib').'Mail\\'.classname($sender['driver']);

        if (! class_exists($driver)) {
            excp('Mail sender not support: '.$sender['driver']);
        }

        return (new $driver)->send($sender, $params);
    }
}
if (! fe('iteratable')) {
    function iteratable($var) {
        if (is_array($var) && $var) {
            return $var;
        }

        return false;
    }
}
if (! fe('cli')) {
    // !!! You can not use `cli()` in \Lif\Core\Strategy\Cli@fire()
    function cli(array $argv = []) {
        $class = 'Lif\Core\Strategy\Cli';
        if (
            isset($GLOBALS['LIF_CLI'])
            && ($GLOBALS['LIF_CLI'] instanceof $class)
       ) {
            $cli = $GLOBALS['LIF_CLI'];
        } else {
            $GLOBALS['LIF_CLI'] = $cli = new $class;
        }

        return $cli
        ->reset()
        ->setArgvs($argv)
        ->fire();
    }
}
if (! fe('to_arr')) {
    function to_arr($arr, $var) {
        return is_array($arr)
        ? array_merge($arr, [$var])
        : [$var];
    }
}
if (! fe('linewrap')) {
    function linewrap(int $cnt = 1) : string {
        $lineWrap = ('web' == context())
        ? '<br>' : PHP_EOL;

        $cnt = ($cnt < 0) ? 1 : $cnt;
        $str = '';

        for ($i=0; $i<$cnt; ++$i) {
            $str .= $lineWrap;
        }

        return $str;
    }
}
if (! fe('space_indent')) {
    function space_indent(int $cnt = 1) : string {
        $tabIndent = ('web' == context())
        ? '&nbsp;' : " ";

        $cnt = ($cnt < 0) ? 1 : $cnt;
        $str = '';

        for ($i=0; $i<$cnt; ++$i) {
            $str .= $tabIndent;
        }

        return $str;
    }
}
if (! fe('tab_indent')) {
    function tab_indent(int $cnt = 1) : string {
        $tabIndent = ('web' == context())
        ? '&nbsp;&nbsp;&nbsp;&nbsp;' : "\t";

        $cnt = ($cnt < 0) ? 1 : $cnt;
        $str = '';

        for ($i=0; $i<$cnt; ++$i) {
            $str .= $tabIndent;
        }

        return $str;
    }
}
if (! fe('char_case_is')) {
    function char_case_is(string $char, $case = 'upper') : bool {
        if (! in_array($case, ['upper', 'lower'])) {
            return false;
        }

        if ('upper' === $case) {
            $asciiStart = 64;
            $asciiEnd   = 91;
            $preg = '/[A-Z]/u';
        } else {
            $asciiStart = 96;
            $asciiEnd   = 123;
            $preg = '/[a-z]/u';
        }

        $arr = str_split($char);
        if (1 === count($arr)) {
            $ascii = ord($char);
            // A-Z ASCII number range => 65~90
            // a-z ASCII number range => 97~122
            return (
                (($asciiStart < $ascii) && ($ascii < $asciiEnd))
                || preg_match($preg, $char)
           );
        }

        return false;
    }
}
if (! fe('ucase_char')) {
    function ucase_char(string $char) : bool {
        return char_case_is($char, 'upper');
    }
}
if (! fe('lcase_char')) {
    function lcase_char(string $char) : bool {
        return char_case_is($char, 'lower');
    }
}
if (! fe('underline2camelcase')) {
    function underline2camelcase(string $underline) {
        if (!is_string($underline)) {
            return false;
        }
        $arr = str_split($underline);
        $len = count($arr);

        if ('_' != $arr[0]) {
            $arr[0] = strtoupper($arr[0]);
        }
        foreach ($arr as $key => $val) {
            if ('_' == $val) {
                if (($key < ($len-1))
                    && ('_' != $arr[$key+1])
                ) {
                    $arr[$key+1] = strtoupper($arr[$key+1]);
                }
                $arr[$key] = '';
            }
            // $camelcase .= $arr[$key];    // slower than implode()
        }
        return implode('', $arr);
    }
}

if (! fe('camelcase2underline')) {
    function camelcase2underline(string $camelcase) {
        $arr = str_split($camelcase);
        foreach ($arr as $key => $val) {
            if (0 == $key) {
                $arr[0] = strtolower($val);
            } else {
                if (ucase_char($val)) {
                    $arr[$key] = '_'.strtolower($val);
                }
            }
        }

        return implode('', $arr);
    }
}
