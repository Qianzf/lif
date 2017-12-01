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
        // ini_set('log_errors', 1);
        // ini_set('error_log', pathOf('log', 'php-errors.log'));
        // set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        // });

        register_shutdown_function(function () {
            if ($error = error_get_last()) {
                logger()->error($error);
            }
        });
        
        set_exception_handler(function ($excp) {
            exception($excp);
        });

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
        session_init();
        load_user_helpers();
    }
}
if (! fe('session_init')) {
    function session_init() : void {
        ini_set('session.name', 'LIFSESSID');
        ini_set('session.cookie_lifetime', 3600);
        ini_set('session.cookie_httponly', true);
    }
}
if (! fe('load_user_helpers')) {
    function load_user_helpers() {
        load_phps(pathOf('aux'), function ($file) {
            include_once $file->getPathname();
        });
    }
}
if (! fe('dd')) {
    function dd(...$args) {
        $GLOBALS['LIF_DEBUGGING'] = true;
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
if (! fe('pt')) {
    function pt(...$args) {
        $GLOBALS['LIF_DEBUGGING'] = true;

        foreach ($args as $arg) {
            print_r($arg);
        }
    }
}
if (! fe('pr')) {
    function pr(...$args) {
        $GLOBALS['LIF_DEBUGGING'] = true;
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
        $GLOBALS['LIF_DEBUGGING'] = true;
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
if (! fe('validate')) {
    function validate(array &$data, array $rules) {
        return \Lif\Core\Facade\Validation::run(
            $data,
            $rules
       );
    }
}
if (! fe('legal_or')) {
    function legal_or(array &$data, array $rulesWithDefaults) : array {
        list($errs, $data) = \Lif\Core\Facade\Validation::runOr(
            $data,
            $rulesWithDefaults
        );

        return $errs;
    }
}
if (! fe('legal_and')) {
    function legal_and(array $data, array $rulesWithVars) {
        if ($rulesWithVars) {
            return \Lif\Core\Facade\Validation::runAnd(
                $data,
                $rulesWithVars
            );
        }

        return true;
    }
}
if (! fe('exists')) {
    // !!! Be careful to check bool value like false
    function exists($var, $idx = null) {
        // !!! Be carefurl if `$var` is array but not an assoc array
        if (is_null($var)) {
            return false;
        }
        if (is_bool($var)) {
            return is_null($idx) ? true : false;
        }
        if (is_scalar($var)) {
            return is_null($idx) ? $var : false;
        }
        if (is_array($var) && !is_null($idx)) {
            $idxes = is_array($idx) ? $idx : [$idx];
            foreach ($idxes as $_idx) {
                if (! isset($var[$_idx])) {
                    return false;
                }
            }
            return (1 === count($idxes)) ? $var[$_idx] : true;
        }
        if (is_callable($var) || ($var instanceof \Closure)) {
            return $idx ? false : ($var ?? false);
        }
        if (is_object($var) && $idx) {
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
    function nsOf(
        string $of = null,
        string $class = '',
        bool $topSlash = true
    ) {
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
                'ctl'  => 'Lif\Ctl\\',
                'mdl'  => 'Lif\Mdl\\',
                'dbvc' => 'Lif\Dat\\Dbvc\\',
                'mdwr' => 'Lif\Mdwr\\',
                'core' => 'Lif\Core\\',
                'web'  => 'Lif\Core\Web\\',
                '_cmd' => 'Lif\Core\Cmd\\',
                'cmd'  => 'Lif\Cmd\\',
                'lib'  => 'Lif\Core\Lib\\',
                '_facade'  => 'Lif\Core\Facade\\',
                'facade'   => 'Lif\Facade\\',
                'queue'    => 'Lif\Core\Queue\\',
                'logger'   => 'Lif\Core\Logger\\',
                'storage'  => 'Lif\Core\Storage\\',
                'strategy' => 'Lif\Core\Strategy\\',
            ];

            $ns = ($nsArr[$of] ?? '').ucfirst($class);

            return $topSlash ? '\\'.$ns : $ns;
        }
    }
}
if (! fe('pathOf')) {
    function pathOf(string $of = null, string $file = '') {
        $root  = realpath(__DIR__.'/../../../');
        $paths = [
            'root'   => $root.'/',
            'app'    => $root.'/app/',
            'dbvc'   => $root.'/app/dat/dbvc/',
            'core'   => $root.'/app/core/',
            'aux'    => $root.'/app/aux/',
            '_aux'   => $root.'/app/core/aux/',
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

        $path = is_null($of) ? $paths : (
            isset($paths[$of]) ? $paths[$of].$file : null
        );

        if (is_string($path) && !file_exists($path)) {
            $arr = explode('/', $path);
            unset($arr[count($arr)-1]);
            $dir = implode('/', $arr);
            if (! file_exists($dir)) {
                // !!! `true` is necessary for recursive creating
                @mkdir($dir, 0775, true);
            }
        }

        return $path;
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
    function _json_encode(array $arr) : string {
        return ($json = json_encode(
            $arr,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        )) ? $json : '';
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
if (! fe('put2file')) {
    function put2file(string $path, $data) {
        file_put_contents(
            $path,
            PHP_EOL.stringify($data).PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
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
        $GLOBALS['LIF_EXCP'] = true;

        // Kill child process if exists
        $children = $GLOBALS['LIF_CHILD_PROCESSES'] ?? false;
        if ($children && is_array($children)) {
            foreach ($children as $child) {
                if (is_integer($child)) {
                    posix_kill($child, SIGKILL);
                }
            }
        }

        $response = $format.'_http_response';
        if (! function_exists($response)) {
            $response = 'json_http_response';
        }

        $info = $_info = [
            'msg' => $exObj->getMessage(),
            'err' => $exObj->getCode(),
        ];
        $_info['trace'] = $exObj->getTrace();
        // $_info['trace'] = explode("\n", $exObj->getTraceAsString());

        // !!! Make sure check app conf path first
        // !!! Or infinite loop will occur when app conf file not exists
        if (('production' != app_env()) && app_debug()) {
            $info['dat']['trace'] = $_info['trace'];
        }

        put2file(
            pathOf('log', 'exceptions/'.date('Y-m-d').'.log'),
            build_log_str($_info, 'exception')
        );
        
        return ('cli' === context())
        ? cli_excp($exObj)
        : $response($info);
    }
}
if (! fe('excp')) {
    function excp($msg, $err = 500, $format = 'json') {
        throw new \Lif\Core\Excp\Lif($msg, $err, $format);
    }
}
if (! fe('build_log_str')) {
    function build_log_str($data, string $level = 'log') : string {
        $timestamp = time();
        $content   = [
            'tdt' => date('H:i:s Y-m-d', $timestamp),
            'tzn' => date_default_timezone_get(),
            'lvl' => $level,
            'tsp' => $timestamp,
            'dat' => $data,
        ];

        return stringify($content);
    }
}
if (! fe('format_ns')) {
    function format_ns($raw) {
        if (is_array($raw) && $raw) {
            return implode(
                '\\',
                array_filter(
                    explode('\\', implode('\\', $raw))
               )
           );
        }
        if (is_string($raw) && $raw) {
            $arr = explode('\\', $raw);
            array_walk($arr, function (&$item, $key) {
                $item = ucfirst($item);
            });

            return implode('\\', array_filter($arr));
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
if (! fe('empty_safe')) {
    function empty_safe($var) {
        if (is_numeric($var)) {
            return false;
        }
        
        return empty($var);
    }
}
if (! fe('array_values_oned')) {
    // Transform multilayers array un-empty values into one dimension
    function array_values_oned(array $arr, array &$ret = []) : array {
        foreach ($arr as $key => $item) {
            if (empty_safe($item)) {
                continue;
            }
            
            if (is_array($item)) {
                array_values_oned($item, $ret);
            } elseif (is_scalar($item)) {
                if (isset($ret[$key])) {
                    $ret[] = $item;
                } else {
                    $ret[$key] = $item;
                }
            }
        }

        return $ret;
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
                $str .= "'".stringify($val)."',\n";
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
if (! fe('arr2code')) {
    function arr2code(array $data, $path) {
        $code = array_stringify($data);
        $code = <<< ARR
<?php

return {$code};\n
ARR;
        file_put_contents($path, $code);
    }
}
if (! fe('cfg')) {
    // Update config file
    function cfg($name, $keyStr, $data) {
        if (!$name
            || !is_string($name)
            || !$keyStr
            || !is_string($keyStr)
            || !$data
        ) {
            throw new \Lif\Core\Excp\Lif('Missing config params');
        }

        $cfgFile = pathOf('conf', $name.'.php');
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
    function conf(
        string $name = null,
        string $cfgPath = null,
        bool $excp = true
    ) : array {
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
            return $excp
            ? excp('Configure File '.$cfgFile.' not exists')
            : [];
        }

        $cfg = include $cfgFile;
        $GLOBALS['LIF_CFG'][$name] = $cfg;

        return $cfg;
    }
}
if (! fe('config')) {
    function config(string $key) {
        return array_query_by_coherent_keys(conf_all(), $key);
    }
}
if (! fe('singleton')) {
    function singleton(
        string $key,
        $class = null,
        bool $flush = null
    ) {
        if (is_null($class)) {
            return \Lif\Core\Factory\Singleton::get($key);
        }

        return \Lif\Core\Factory\Singleton::set($key, $class, $flush);
    }
}
if (! fe('build_pdo_dsn')) {
    // !!! $conn => must `validate_db_conn` first
    function build_pdo_dsn($conn) {
        $dsn = $conn['driver'].':';

        switch ($conn['driver']) {
            case 'mysql':
                $dsn .= 'host='
                .$conn['host'];
                $dsn .= ';port='.(exists($conn, 'port')
                    ? $conn['port']
                    : 'utf8'
                );
                $dsn .= ';charset='.(exists($conn, 'charset')
                    ? $conn['charset']
                    : 'utf8'
                );
                $dsn .= exists($conn, 'dbname')
                ? ';dbname='.$conn['dbname'] : '';
                break;
            case 'sqlite':
                if (exists($conn, 'memory')) {
                    $dsn .= ':memory:';
                } else {
                    $dsn .= $path = pathOf('root').$conn['path'];
                    if (! file_exists($path)) {
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
if (! fe('create_dbconn')) {
    function create_dbconn(&$conn) {
        $dsn  = build_pdo_dsn(validate_db_conn($conn));
        $opts = ('cli' == context())
        ? [\PDO::ATTR_PERSISTENT => true]
        : [];

        return [$dsn, $opts];
    }
}
if (! fe('create_ldo')) {
    function create_ldo($conn) {
        list($dsn, $opts) = create_dbconn($conn);
        $ldo = new \Lif\Core\Storage\LDO(
            $dsn,
            $conn['user'],
            $conn['passwd'],
            $opts
        );

        $ldo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $ldo->setConn($conn['name']);

        return $ldo;
    }
}
if (! fe('create_pdo')) {
    function create_pdo($conn) {
        list($dsn, $opts) = create_dbconn($conn);
        $pdo = new \PDO(
            $dsn,
            $conn['user'],
            $conn['passwd'],
            $opts
        );

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
if (! fe('pdo')) {
    function pdo(string $conn = null, bool $flush = null) {
        return \Lif\Core\Factory\Storage::fetch('db', 'pdo', [
            'conn'  => $conn,
            'flush' => $flush,
        ]);
    }
}
if (! fe('ldo')) {
    function ldo(string $conn = null, bool $flush = null) {
        return \Lif\Core\Factory\Storage::fetch('db', 'ldo', [
            'conn'  => $conn,
            'flush' => $flush,
        ]);
    }
}
if (! fe('db')) {
    function db(string $conn = null, bool $flush = false) {
        return (
            new \Lif\Core\Storage\SQL\Builder($conn, $flush)
        );
    }
}
if (! fe('db_conns')) {
    function db_conns() {
        return \Lif\Core\Factory\Storage::fetch('db', 'conns');
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
if (! fe('schema')) {
    function schema(string $conn = null) {
        return (
            new \Lif\Core\Storage\SQL\Schema($conn)
        )
        ->setConn($conn);
    }
}
if (! fe('escape_fields')) {
    function escape_fields(string $raw) : string
    {
        if (! $raw) {
            return '';
        }

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
if (! fe('load_phps')) {
    function load_phps(string $path, \Closure $callable) {
        if (! file_exists($path)) {
            excp('PHP files path not exists: '.$path);
        }

        $result = [];
        $fsi = new \FilesystemIterator($path);
        foreach ($fsi as $file) {
            if ($file->isFile()) {
                if ('php' == $file->getExtension()) {
                    $result[$file->getPathname()] = $callable($file);
                }
            } elseif ($file->isDir()) {
                $_path = $path.'/'.$file->getBasename();
                load_phps($_path, $callable);
            }
        }

        unset($fsi);

        return $result;
    }
}
if (! fe('load_array')) {
    function load_array(string $path, array &$msg = []) : array {
        load_phps($path, function ($file) use (&$msg) {
            $_msg = include $file->getPathname();
            if ($_msg && is_array($_msg)) {
                $msg = array_merge($_msg, $msg);
            }
        }, false);

        return $msg;
    }
}
if (! fe('load_object')) {
    function load_object(string $path, \Closure $callable) {
        load_phps($path, function ($file) use ($path, $callable) {
            $arr = explode('.', $file->getBasename());
            if (! ($class = $arr[0] ?? null)) {
                excp('Illegal class name: '. $class);
            }

            $callable($class, $path);
        });
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
            $arr  = array_filter(explode('_', $key));
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
if (! fe('L')) {
    function L($key, $msg = null, $lang = null) {
        return lang($key, $msg, $lang);
    }
}
if (! fe('lang')) {
    function lang($key, $msg = null, $lang = null) {
        return (mb_strlen(($_msg = stringify($msg))) > 0)
        ? sysmsg($key, $lang).': '.$_msg
        : sysmsg($key, $lang);
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
if (! fe('is_closure')) {
    function is_closure($var) {
        return is_object($var) && ($var instanceof \Closure);
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
if (! fe('segstr')) {
    function segstr(string $string) : string {
        return linewrap().$string.linewrap();
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
if (! fe('tabdent')) {
    function tabdent(int $cnt = 1) : string {
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
if (! fe('between')) {
    function between($num, $start, $end) : bool {
        return (($start <= $num) && ($num <= $end));
    }
}
if (! fe('queue_default_defs_get')) {
    function queue_default_defs_get() {
        return [
            'id',
            'queue',
            'detail',
            'try',
            'tried',
            'retried',
            'create_at',
            'timeout',
            'restart',
            'lock',
        ];
    }
}
if (! fe('logger_conf')) {
    function logger_conf(string $logger = null) {
        $conf = conf('log', null, false);

        if ($conf) {
            if (true !== ($err = validate($conf, [
                'default' => 'need|string',
                'loggers' => 'need|array',
            ]))) {
                excp('Illegal log configurations: '.$err);
            }

            $logger = $logger ?? $conf['default'];

            if (true !== ($err = validate($conf['loggers'], [
                $logger => 'need|array',
            ]))) {
                excp('Illegal or missing logger configurations: '
                    .$err
                );
            }

            return $conf['loggers'][$logger];
        }

        return $conf;
    }
}
if (! fe('logger')) {
    // String => logger key in app/conf/log.php
    // Array  => dynamic logger configs
    function logger($log = null) {
        if (! $log) {
            $config = ($conf = logger_conf())
            ? $conf : [
                'driver' => 'file',
                'path'   => 'lif.log',
            ];
        } elseif (is_string($log)) {
            $config = logger_conf($log);
        } elseif (is_array($log)) {
            $config = $log;
        } elseif ($log instanceof \Lif\Core\Intf\Logger) {
            return $log;
        }
        
        if (true !== ($err = validate($config, [
            'driver' => 'need|in:file,db'
        ]))) {
            excp('Missing or un-supported logger type: '.$err);
        }

        $class = nsOf('logger', $config['driver']);

        if (! class_exists($class)) {
            excp('Logger class not exists: '.$class);
        }

        if (isset($GLOBALS['LIF_LOGGER'][$config['driver']])
            && ($_logger = $GLOBALS['LIF_LOGGER'][$config['driver']])
            && ($_logger instanceof \Lif\Core\Intf\Logger)
        ) {
            $logger = $_logger;
        } else {
           $logger
           = $GLOBALS['LIF_LOGGER'][$config['driver']]
           = new $class($config);
        }

        return $logger;
    }
}
if (! fe('logging')) {
    function logging(
        $message = null,
        array $context = [],
        string $level = 'log',
        $logger = null
    ) {
        $logger = logger($logger);

        if (0 === func_num_args()) {
            return $logger;
        }

        $logger->log($level, $message, $context);
    }
}
if (! fe('str_with_context')) {
    function str_with_context(
        $message,
        array $context = []
    ) : string {
        $string = stringify($message);
        if (! $context) {
            return $string;
        }
        foreach ($context as $key => $val) {
            if (! safe_string($key)) {
                excp('String value is unsafe: '.$key);
            }

            $ptn = '/\{'.$key.'\}/u';
            $string = preg_replace_callback(
                $ptn,
                function ($matches) use ($val) : string {
                    return stringify($val);
                },
                $string
            );
        }

        return $string;
    }
}
if (! fe('safe_string')) {
    function safe_string(string $str) {
        return preg_match('/\w+/u', $str);
    }
}
if (! fe('stringify')) {
    function stringify($origin) : string {
        if ('' === $origin || is_null($origin)) {
            return '';
        }
        if (is_scalar($origin)) {
            return ((string) $origin);
        }
        if (is_array($origin)) {
            return _json_encode($origin);
        }
        if (is_object($origin)) {
            $class = get_class($origin);
            if (method_exists($origin, '__toString')) {
                if (! is_string($ret = call_user_func([$origin, '__toString']))) {
                    excp('Bad __toString() definition of class: '.$class);
                }

                return $ret;
            }

            excp(
                'Object of '
                .$class
                .' is unstringable.'
            );
        }
    }
}
if (! fe('build_cmds_with_env')) {
    function build_cmds_with_env($cmds) : string {
        $_cmds = build_cmds($cmds);
        return 'export PATH='
        .implode(':', [
            '/bin',
            '/sbin',
            '/usr/bin',
            '/usr/sbin',
            '/usr/local/bin',
            '/usr/local/sbin',
            '/usr/local/php/bin',
            '~/bin',
        ])
        .' && '
        .$_cmds;
    }
}
if (! fe('build_cmds')) {
    function build_cmds($cmds) {
        if (! $cmds) {
            excp('No commands to execute.');            
        }
        if (is_string($cmds)) {
            return '('.$cmds.')';
        }

        if (is_array($cmds)) {
            return '('.implode(' && ', $cmds).')';
        }

        excp('Illegal commands type, require string or array');
    }
}
if (! fe('proc_exec')) {
    function proc_exec($cmds, string $workdir = null) {
        if (!fe('proc_open') || !fe('proc_close')) {
            excp(
                'PHP function `proc_open()` was disabled'
            );
        }

        if (is_null($workdir)) {
            $workdir = __DIR__;
        }

        $descriptorspec = [
            0 => ['pipe', 'r'],  // std-in
            1 => ['pipe', 'w'],  // std-out
            2 => ['pipe', 'w'],  // std-err
        ];

        $process = proc_open(
            build_cmds($cmds),
            $descriptorspec,
            $pipes,
            $workdir,
            null
        );

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        return [
            'num' => proc_close($process),
            'out' => trim($stdout),
            'err' => trim($stderr),
        ];
    }
}
if (! fe('legal_server')) {
    // Validate SSH server configs
    function legal_server(array &$config) {
        if (true !== ($err = validate($config, [
            'host' => 'need|host',
            'port' => ['int|min:1', 22],
            'auth' => ['in:pswd,pki', 'pki'],
            'user' => ['string', 'root'],
            'pswd' => 'when:auth=pswd|string',
            'pubk' => 'when:auth=pki|string',
            'prik' => 'when:auth=pki|string',
        ]))) {
            excp('Illegal SSH server configs: '.$err);
        }

        return true;
    }
}
if (! fe('ssh_conf')) {
    function ssh_conf($server = null) {
        if ($server && is_array($server)) {
            return $server;
        }

        $conf = conf('ssh', null, false);

        if ($conf) {
            if (true !== ($err = validate($conf, [
                'default' => 'need|string',
                'servers' => 'need|array',
            ]))) {
                excp('Illegal SSH servers configurations: '.$err);
            }

            $server = is_string($server) && $server
            ? $server
            : $conf['default'];

            if (true !== ($err = validate($conf['servers'], [
                $server => 'need|array',
            ]))) {
                excp('Illegal or missing SSH server configurations: '
                    .$err
                );
            }

            return $conf['servers'][$server];
        }

        return $conf;
    }
}
if (! fe('ssh_exec')) {
    function ssh_exec($cmds, $config = null) {
        return (
            new \Lif\Core\Cli\SSH(ssh_conf($config))
        )->exec($cmds);
    }
}
if (! fe('ssh_exec_array')) {
    function ssh_exec_array(array $cmds, $config = null) {
       $ssh = new \Lif\Core\Cli\SSH(ssh_conf($config));

       foreach ($cmds as $key => $cmd) {
            $ret        = $ssh->exec($cmd);
            $ret['cmd'] = $cmd;
            
            if ($ret['num'] != 0) {
                return $ret;
            }
       }

       return true;
    }
}