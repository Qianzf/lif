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
                    excp('Missing validator: '.($_ruleArr[0] ?? 'unknown'));
                }

                $validator = $_ruleArr[0];
                $extra     = $_ruleArr[1] ?? null;

                if (('need' != $validator)) {
                    if (! isset($data[$key])) {
                        continue;
                    }

                    if (true !== ($err = $this->$validator(
                        $data[$key],
                        $extra
                    ))) {
                        return is_string($err)
                        ? $err
                        : 'ILLEGAL_'.strtoupper($key);
                    }
                } else {
                    if (true !== ($err = $this->need($key, $data))) {
                        return $err;
                    }
                }
            }
        }

        return true;
    }

    // Check existing and un-empty value
    public function need(string $key, array $data)
    {
        return (isset($data[$key]) && !empty($data[$key]))
        ? true
        : 'MISSING_'.strtoupper($key);
    }

    // If has email field then check it
    // Or always return true
    public function email($value, $extra = null)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'ILLEGAL_EMAIL';
        }

        return true;
    }

    public function string($value, $extra = null)
    {
        return is_string($value);
    }

    public function array($value, $extra = null)
    {
        return is_array($value);
    }

    public function int($value, $extra = null)
    {
        return is_numeric($value) && (intval($value) == $value);
    }

    public function min($value, $min)
    {
        if (! is_numeric($min)) {
            return 'ILLEGAL_MIN_VALUE';
        }

        if (is_numeric($value)) {
            return ($value >= $min);
        } elseif (is_string($value)) {
            return true;    // TODO
        }
    }

    public function in($value, $in)
    {
        if (!is_array($in) && !is_string($in)) {
            return 'ILLEGAL_VALUE_RANGE';
        }

        if (is_string($in)) {
            $in = explode(',', $in);
        }

        return in_array($value, $in);
    }

    public function domain()
    {
        // TODO
        return true;
    }
}
