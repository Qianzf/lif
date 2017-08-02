<?php

// ----------------------------------------------
//     Helper Functions for Web Scenario Only
// ----------------------------------------------

if (!function_exists('getallheaders')) {
    // For nginx, compatible with apache format
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (mb_substr($name, 0, 5) === 'HTTP_') {
                $headers[mb_substr($name, 5)] = $value;
            }
        }
        return $headers;
    }
}
if (!function_exists('response')) {
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
            json_http_response(_json_encode($info));
        }
    }
}
if (!function_exists('legal_route_binding')) {
    function legal_route_binding($routeBind)
    {
        if (is_callable($routeBind)) {
            return true;
        }
        if (is_string($routeBind)) {
            return preg_match('/^((\\\\)*[A-Z]\w*)*\@\w+$/u', $routeBind);
        }

        return false;
    }
}
if (!function_exists('legal_http_methods')) {
    function legal_http_methods()
    {
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
if (!function_exists('client_error')) {
    // ----------------------------------------------------------------------
    //     PHP errors caused by client behaviours called client error
    //     eg: route not found, params illegal, etc.
    // ----------------------------------------------------------------------
    //     Client error is used to tell cllient what's going wrong
    //     Debug model or environment will not effect client error output
    // ----------------------------------------------------------------------
    function client_error($msg, $err)
    {
        response([], $msg, $err);
    }
}
if (!function_exists('format_route_key')) {
    function format_route_key($route)
    {
        $routeKey = implode('.', array_filter(explode('/', $route)));
        return $routeKey ? $routeKey : '.';
    }
}
if (!function_exists('escape_route_name')) {
    function escape_route_name($name)
    {
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
if (!function_exists('get_raw_route')) {
    function get_raw_route($key)
    {
        if ('.' === $key) {
            return '/';
        }

        return '/'.str_replace('.', '/', $key);
    }
}
