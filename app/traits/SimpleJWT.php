<?php

// -------------------------------------
//     Default Json Web Token in LiF
// -------------------------------------

namespace Lif\Traits;

trait SimpleJWT
{
    public function issue($params = [])
    {
        $header  = base64_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'SHA256',
        ]));
        $timestamp = time();
        $claims = [
            'exp' => $timestamp+3600,
            'nbf' => $timestamp,
            'iat' => $timestamp,
        ];
        $payload    = base64_encode(json_encode(array_merge(
            $params,
            $claims
        )));
        $signature  = base64_encode(hash_hmac(
            'sha256',
            $header.'.'.$payload,
            $this->getSecureKeyOfOldSys()
        ));

        return implode('.', [$header, $payload, $signature]);
    }

    public function getSecureKeyOfOldSys()
    {
        $sk = (!($_sk = exists(conf('app'), 'JWT_SK')) || !is_string($_sk))
        ? '' : $_sk;

        return $sk;
    }

    public function check($jwt)
    {
        $jwtComponents = explode('.', $jwt);
        if (3 != count($jwtComponents)) {
            return false;
        }

        list($header, $payload, $signature) = $jwtComponents;
        if ($headerArr = json_decode(base64_decode($header), true)) {
            if (is_array($headerArr) && isset($headerArr['alg'])) {
                $alg = strtolower($headerArr['alg']);
                if (in_array($alg, hash_algos())) {
                    if (base64_decode($signature) === hash_hmac(
                        $alg,
                        $header.'.'.$payload,
                        $this->getSecureKeyOfOldSys())
                    ) {
                        $data = json_decode(base64_decode($payload), true);
                        // Missing expire date or wrong JWT
                        if (! isset($data['exp'])
                            && ! is_timestamp($data['exp'])
                        ) {
                            return false;
                        }
                        // JWT expired
                        // !!! Make sure equal timezone were used both in JWT issuing and JWT checking
                        if (time() > $data['exp']) {
                            return false;
                        }

                        return $data;
                    }
                }
            }
        }

        return false;
    }

    // Authorise in HTTP HEADER `AUTHORIZATION`
    public function authorise($headers = null)
    {
        if (is_null($headers)) {
            if (! ($authorization = exists($_SERVER, 'HTTP_AUTHORIZATION'))) {
                return false;
            }
        } else {
            if (! ($authorization = exists($headers, 'AUTHORIZATION'))) {
                return false;
            }
        }

        return $this->check($authorization);
    }
}
