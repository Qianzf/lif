<?php

if (!function_exists('dd')) {
	function dd($var) {
		var_dump($var);
		exit;
	}
}

if (!function_exists('response')) {
	function response($dat = [], $msg = 'ok', $err = 200, $format = 'json') {
        if ('json' === $format) {
            header('Content-type:application/json; charset=UTF-8');

            exit(json_encode([
                'err' => $err,
                'msg' => $msg,
                'dat' => (array) $dat,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }
}

if (!function_exists('error')) {
    function error($err, $msg) {
        response([], $msg, $err);
    }
}

if (!function_exists('array_stringify_main')) {
    function array_stringify_main($arr, &$level) {
        $str = '';
        $margin = str_repeat("\t", $level++);
        foreach ($arr as $key => $val) {
            $str .= $margin."'".$key."' => ";
            if (is_array($val)) {
                $str .= "[\n";
                $str .= array_stringify_main($val, $level);
                $str .= $margin."],\n";
                $level--;
            } else {
                $str .= "'".$val."',\n";
            }
        }
        return $str;
    }
}

if (!function_exists('array_stringify')) {
    function array_stringify($arr, $level) {
        $str  = "[\n";
        $str .= array_stringify_main($arr, $level);
        $str .= ']';

        return $str;
    }
}

if (!function_exists('config')) {
    function config($key = null, $val = null) {
        global $_LIF_CONFIG;
        if (is_null($key)) {
            return $_LIF_CONFIG;
        }
        if ($key && !$val) {
            return $_LIF_CONFIG[$key] ?? null;
        }
        if ($key && $val) {
            if (!isset($_LIF_CONFIG[$key])) {
                return false;
            }
            $_LIF_CONFIG[$key] = $val;
            $level   = 1;
            $cfg  = array_stringify($_LIF_CONFIG, $level);
            $_cfg = <<< CFG
<?php

return {$cfg};\n
CFG;
            return file_put_contents(__DIR__.'/../cfg.php', $_cfg, LOCK_EX);
        }
    }
}
