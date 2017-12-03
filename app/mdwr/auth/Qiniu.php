<?php

// Qiniu service auth control
// Via access token and secret token
// @cjli

namespace Lif\Mdwr\Auth;

use \Qiniu\Auth;

class Qiniu extends \Lif\Core\Abst\Middleware
{
    private $ak = false;
    private $sk = false;
    private $bucket = null;

    public function __construct()
    {
        try {
            list(
                $this->ak,
                $this->sk,
                $this->bucket,
            ) = [
                config('qiniu.ak', false),
                config('qiniu.sk', false),
                config('qiniu.bucket', 'hcmchi'),
            ];
        } catch (\Exception $e) {
        } finally {
        }
    }

    public function passing($app)
    {
        if ((false !== $this->ak) && (false !== $this->sk)) {
            $qiniu = new Auth($this->ak, $this->sk);

            $qiniu->bucket = $this->bucket;

            return $qiniu;
        }

        return response([], 'Missing or illegal qiniu configurations.', 503);
    }
}
