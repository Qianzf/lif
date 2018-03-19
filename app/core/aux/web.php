<?php

// ------------------------------------------
//     Helper Functions for Web Scenarios
// ------------------------------------------

if (! fe('is_ajax')) {
    function is_ajax() {
        return (
            strtolower(server('HTTP_X_REQUESTED_WITH')
            ) === 'xmlhttprequest'
        );
    }
}
if (! fe('csrf_token')) {
    function csrf_token() {
        $key  = stringify(config('app.csrf.key') ?? '');
        $data = time().':'.uniqid(getmypid());

        return $data.'.'.sha1($data.'$'.$key);
    }
}
if (! fe('csrf_field')) {
    function csrf_field() {
        $token = csrf_token();
        $input = "<input type='hidden' name='__rftkn__' value='{$token}'>";

        return $input;
    }
}
if (! fe('server')) {
    function server(string $key = null, $val = null) {
        return (
            $key ? ($_SERVER[$key] ?? null) : collect($_SERVER)
        ) ?? $val;
    }
}
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
            $info['dat'] = $dat;
        }

        if ('json' === $format) {
            json_http_response($info);
        }
    }
}
if (! fe('abort')) {
    function abort(int $status = 403, string $msg = '') {
        ob_start();
        ob_end_clean();

        // header("HTTP/1.1 {$status}");
        http_response_code($status);

        $data = [
            'err' => $status,
            'msg' => $msg
        ];

        json_http_response($data);
    }
}
if (! fe('legal_route_binding')) {
    function legal_route_binding($routeBind) {
        if (is_callable($routeBind)) {
            return true;
        }
        if (is_string($routeBind)) {
            return preg_match('/^((\\\\)*[A-Z]\w*)*\@?\w+$/u', $routeBind);
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
    function client_error($msg, int $err = 403) {
        put2file(
            pathOf('log', 'errors/'.date('Y-m-d').'.log'),
            build_log_str([
                'err' => $err,
                'msg' => $msg,
            ], 'error')
        );

        abort($err, $msg);
    }
}
if (! fe('filter_route')) {
    function filter_route(string $route) : array {
        return array_filter(
            explode('/', $route),
            function (&$item) {
                // Just filter empty string
                return ('' != $item);
            }
        );
    }
}
if (! fe('format_route_key')) {
    function format_route_key(string $route) {
        $routeKey = implode(
            '.',
            filter_route($route)
        );
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
if (! fe('uri')) {
    function uri(string $route, array $params = [], array $queies = []) {
        $uri = $route;

        if ($params) {
            $idx = 0;
            $uri = preg_replace_callback('/\?/u',
                function ($matches) use ($params, &$idx) {
                    return $params[$idx++] ?? null;
                }, $route
            );

            unset($idx);
        }

        if ($queies && ($string = http_build_query($queies))) {
            $uri .= $string;
        }

        return urldecode($uri);
    }
}
if (! fe('route')) {
    function route(string $alias, ...$params) : string {
        $route = $GLOBALS['LIF_ROUTES_ALIASES'][$alias]['route'] ?? false;

        if (false === $route) {
            excp('Route alias not found: '.$alias);
        }

        $assoc = (1 === count($params)
            && ($params = ($params[0] ?? null))
            && is_array($params)
        );

        // check if route has parameters
        $idx = 0;
        $route = preg_replace_callback(
            '/\{(\w+)\}/u',
            function ($matches) use ($alias, $assoc, $params, &$idx) {
                if ($key = ($matches[1] ?? null)) {
                    if (($assoc && (
                        !isset($params[$key]) || !($value = $params[$key])
                    )) || (!$assoc && (
                        !isset($params[$idx]) || !($value = $params[$idx++])
                    ))) {
                        excp(
                            'Missing route parameter for alias: '
                            .$alias
                        );
                    }

                    return $value;
                } else {
                    excp('Illegal route parameter definition.');
                }
            },
            $route
        );

        unset($idx);

        return get_raw_route($route);
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
if (! fe('redirect')) {
    function redirect($uri) {
        header('Location: '.$uri);

        exit;
    }
}
if (! fe('shares')) {
    function shares(array $data = []) {
        return session()->sets($data);
    }
}
if (! fe('share_error')) {
    function share_error($data) {
        share('__error', $data);
    }
}
if (! fe('share_error_i18n')) {
    function share_error_i18n(string $key) {
        share('__error', L($key));
    }
}
if (! fe('share_flush')) {
    function share_flush(string $key) {
        return session()->flush($key);
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
if (! fe('syslang')) {
    function syslang() {
        return $_REQUEST['lang'] ?? (
            session()->get('__lang') ?? 'zh'
        );
    }
}
if (! fe('view')) {
    function view(string $template, array $data = [], $cache = false) {
        return (
            new \Lif\Core\Web\View($template, $data, $cache)
        );
    }
}
if (! fe('js')) {
    function js($js) {
        echo _js($js);
    }
}
if (! fe('css')) {
    function css($js) {
        echo _css($js);
    }
}
if (! fe('_js')) {
    function _js($js) {
        if (! $js) {
            return null;
        }

        if (is_string($js)) {
            $path = ('/' == mb_substr($js, 0, 1))
            ? $js.'.js'
            : '/assets/'.$js.'.js';
            
            return '<script src="'.$path.'"></script>'.PHP_EOL;
        } elseif (is_array($js)) {
            $script = '';
            foreach ($js as $name) {
                $path    = ('/' == mb_substr($name, 0, 1))
                ? $name.'.js'
                : '/assets/'.$name.'.js';
                $script .= '<script src="'.$path.'"></script>'.PHP_EOL;
            }

            return $script;
        }

        return null;
    }
}
if (! fe('_css')) {
    function _css($css) {
        if (! $css) {
            return null;
        }

        if (is_string($css)) {
            $path = ('/' == mb_substr($css, 0, 1))
            ? $css.'.css'
            : '/assets/'.$css.'.css';

            return '<link rel="stylesheet" href="'.$path.'">'.PHP_EOL;
        } elseif (is_array($css)) {
            $style = '';
            foreach ($css as $name) {
                $path    = ('/' == mb_substr($name, 0, 1))
                ? $name.'.css'
                : '/assets/'.$name.'.css';

                $style .= '<link rel="stylesheet" href="'.$path.'">'.PHP_EOL;
            }

            return $style;
        }

        return null;
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
