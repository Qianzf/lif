<?php

// ---------------------------
//     Web CSRF protection
// ---------------------------

namespace Lif\Core\Web;

class CSRF implements \Lif\Core\Intf\Middleware
{
    private $allowedMethods = [
        'GET',
        'HEAD',
        'OPTIONS',
    ];
    private $expire = 60;

    public function handle($app)
    {
        if (in_array(server('REQUEST_METHOD'), $this->allowedMethods)) {
        } else {
            if ($token = $app->request->magic('__rftkn__')) {
                list($data, $hash)  = explode('.', $token);
                list($time, $nonce) = explode(':', $data);

                if (($time+$this->getExpire()) < time()) {
                    $this->error('Expired CSRF token');
                }

                $key = stringify(config('app.csrf.key') ?? '');
                if ($hash !== sha1($data.'$'.$key)) {
                    $this->error('CSRF token illegal');
                }
            } else {
                $this->error('Missing CSRF token');
            }
        }

        return true;
    }

    private function error(string $err)
    {
        client_error("Unsafe request: $err.");
    }

    private function getExpire()
    {
        return config('app.csrf.expire') ?? $this->expire;
    }
}
