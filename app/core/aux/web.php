<?php

// ----------------------------------------------
//     Helper Functions for Web Scenario Only
// ----------------------------------------------

if (! fe('getallheaders')) {
    // For nginx, compatible with apache format
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (mb_substr($name, 0, 5) === 'HTTP_') {
                $headers[mb_substr($name, 5)] = $value;
            }
        }
        return $headers;
    }
}
if (! fe('response')) {
    function response(
        $dat = [],
        $msg = 'ok',
        $err = 0,
        $format = 'json'
    ) {
        $info = [
            'err' => $err,
            'msg' => $msg,
        ];

        if ($dat) {
            $info['dat'] = (array) $dat;
        }

        if ('json' === $format) {
            json_http_response($info);
        }
    }
}
if (! fe('abort')) {
    function abort($status = 403, $msg = '') {
        ob_start();
        ob_end_clean();
        header('HTTP/1.1 '.$status);
        exit(json_http_response([
            'Warning' => $msg.' ('.$status.')'
        ]));
    }
}
if (! fe('legal_route_binding')) {
    function legal_route_binding($routeBind) {
        if (is_callable($routeBind)) {
            return true;
        }
        if (is_string($routeBind)) {
            return preg_match('/^((\\\\)*[A-Z]\w*)*\@\w+$/u', $routeBind);
        }

        return false;
    }
}
if (! fe('legal_http_methods')) {
    function legal_http_methods() {
        return [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'OPTIONS',
            'HEAD',
        ];
    }
}
if (! fe('client_error')) {
    // ----------------------------------------------------------------------
    //     PHP errors caused by client behaviours called client error
    //     eg: route not found, params illegal, etc.
    // ----------------------------------------------------------------------
    //     Client error is used to tell cllient what's going wrong
    //     Debug model or environment will not effect client error output
    // ----------------------------------------------------------------------
    function client_error($msg, $err) {
        abort($err, $msg);
    }
}
if (! fe('format_route_key')) {
    function format_route_key($route) {
        $routeKey = implode('.', array_filter(explode('/', $route)));
        return $routeKey ? $routeKey : '.';
    }
}
if (! fe('escape_route_name')) {
    function escape_route_name($name) {
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            if (is_array($matches) &&
                isset($matches[1]) &&
                is_string($matches[1]) &&
                $matches[1]
            ) {
                return '{?}';
            }
        }, $name);
    }
}
if (! fe('route')) {
    function route($alias) : string {
        $route = $GLOBALS['LIF_ROUTES_ALIASES'][$alias] ?? false;

        if (false === $route) {
            excp('Route alias not found for `'.$alias.'`');
        }

        return get_raw_route($route['route']);
    }
}
if (! fe('get_raw_route')) {
    function get_raw_route($key) {
        if (! is_scalar($key)) {
            excp(
                'Illegal route key.'
            );
        }

        if ('.' === $key) {
            return '/';
        }

        return '/'.str_replace('.', '/', $key);
    }
}
if (! fe('try_client_ip_key')) {
    function try_client_ip_key($possible_key) {
        return getenv($possible_key)
        ?? (
            $_SERVER[$possible_key] ?? null
        );
    }
}
if (! fe('redirect')) {
    function redirect($uri) {
        header('Location: '.$uri);

        exit;
    }
}
if (! fe('ip_of_client')) {
    function ip_of_client() {
        $clientIPKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($clientIPKeys as $key) {
            if ($clientIP = try_client_ip_key($key)) {
                break;
            }
        }

        return $clientIP ?? 'UNKNOWN';
    }
}
if (! fe('share')) {
    function share(string $key, $val = null, bool $delete = false) {
        $session = session();

        if ($delete) {
            return $session->delete($key);
        } elseif (! is_null($val)) {
            return $session->set($key, $val);
        } else {
            return $session->get($key);
        }
    }
}
if (! fe('share_flush')) {
    function share_flush(string $key) {
        return session()->flush($key);
    }
}
if (! fe('syslang')) {
    function syslang() {
        return $_REQUEST['lang'] ?? (
            session()->get('__lang') ?? 'zh'
        );
    }
}
if (! fe('session')) {
    function session() {
        $session = $GLOBALS['LIF_SESSION'] ?? null;
        if (! $session || !is_object($session)) {
            $GLOBALS['LIF_SESSION'] = $session = new \Lif\Core\Web\Session;
        }

        return $session;
    }
}
if (! fe('is_mobile_device')) {
    function is_mobile_device() {
        if (! ($ua = $_SERVER['HTTP_USER_AGENT']) || !is_string($ua)) {
            return false;
        }

        return (
            preg_match(
                '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',
                $ua
        ) || preg_match(
            '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
            substr($ua, 0, 4)
        ));
    }
}
