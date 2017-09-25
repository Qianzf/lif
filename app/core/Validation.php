<?php

namespace Lif\Core;

class Validation
{
    public function run(array $data, array $rules)
    {
        foreach ($rules as $key => $rule) {
            $_rules = explode('|', $rule);

            foreach ($_rules as $_rule) {
                $_ruleArr = explode(':', $_rule);
                if (!isset($_ruleArr[0])
                    || !method_exists($this, $_ruleArr[0])
                ) {
                    excp('Missing validator: `'.($_ruleArr[0] ?? 'unknown'));
                }

                $validator = $_ruleArr[0];
                $extra     = $_ruleArr[1] ?? null;

                if (true !== ($err = $this->$validator(
                    $key,
                    $data,
                    $extra
                ))) {
                    return $err;
                }
            }
        }

        return true;
    }

    // Check existing and un-empty value
    public function need(string $key, array $data, $extra = null)
    {
        return (isset($data[$key]) && !empty($data[$key]))
        ? true
        : 'MISSING_'.strtoupper($key);
    }

    // If has email field then check it
    // Or always return true
    public function email(string $key, array $data, array $extra = null)
    {
        if (isset($data[$key]) || is_null($data[$key])) {
            if (! $data[$key]) {
                return true;
            }
            if (false === filter_var($data[$key], FILTER_VALIDATE_EMAIL)) {
                return 'ILLEGAL_EMAIL';
            }
        }

        return true;
    }

    public function in(string $key, array $data, $in)
    {
        if (isset($data[$key]) || is_null($data[$key])) {
            if (! $data[$key]) {
                return true;
            }
            if (!is_array($in) && !is_string($in)) {
                return 'ILLEGAL_VALUE_RANGE';
            }
        }

        if (is_string($in)) {
            $in = explode(',', $in);
        }

        $err = 'ILLEGAL_'.strtoupper($key);

        return in_array($data[$key], $in) ? true : $err;
    }
}
