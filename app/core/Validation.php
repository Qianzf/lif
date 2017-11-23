<?php

namespace Lif\Core;

class Validation
{
    public function exec(array $data, array $rules)
    {
        $this->run($data, $rules);

        return $data;
    }

    public function run(array &$data, array $rules)
    {
        foreach ($rules as $key => $item) {
            unset($hasDefault);
            if (is_string($item)) {
                $rule = $item;
            } elseif (is_array($item)) {
                if (! ($rule = $item[0] ?? ($item['rule'] ?? null))) {
                    excp('Missing validation rule.');
                }
                $hasDefault = $item[1] ?? ($item['default'] ?? null);
            } else {
                excp('Single validation rule must be a string or array.');
            }

            $_rules = array_filter(explode('|', $rule));

            foreach ($_rules as $_rule) {
                $_ruleArr = array_filter(explode(':', $_rule));

                if (!isset($_ruleArr[0])
                    || !method_exists($this, $_ruleArr[0])
                ) {
                    excp('Missing validator: '.($_ruleArr[0] ?? 'unknown'));
                }

                $validator = $_ruleArr[0];
                $extra     = $_ruleArr[1] ?? null;
                $isWhen    = ('when' === strtolower($validator));
                $necessary = in_array('need', $_rules);
                $hasKey    = isset($data[$key]);

                if (true !== ($err = $this->$validator(
                    ($data[$key] ?? null),
                    $extra,
                    $data,
                    $key
                ))) {
                    if (isset($hasDefault)) {
                        $data[$key] = $hasDefault;
                    }

                    if (! $hasKey) {
                        break;
                    }

                    if (! $necessary) {
                        if ($isWhen) {
                            if (-1 === $err) {
                                break;
                            } elseif (1 === $err) {
                                continue;
                            }
                        } else {
                            break;
                        }
                    }

                    return is_string($err)
                    ? $err : 'ILLEGAL_'.strtoupper($key);
                }
            }
        }

        return true;
    }

    // Check existing and un-empty value
    public function need(
        $vaule = null,
        $extra = null,
        array $data,
        string $key
    ) {
        return (isset($data[$key]) && !empty_safe($data[$key]))
        ? true
        : 'MISSING_OR_EMPTY_'.strtoupper($key);
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
            // TODO
            return true;
        }
    }

    public function max($value, $max)
    {
        if (! is_numeric($max)) {
            return 'ILLEGAL_MAX_VALUE';
        }

        return is_numeric($value)
        ? ($value <= $max)
        : 'ILLEGAL_VALUE';
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

    public function when($value, $extra, array $data, string $key)
    {
        $cond = explode('=', $extra);

        if (! isset($cond[0]) || !is_string($cond[0])) {
            return 'MISSING_OR_ILLEGAL_WHEN_FIELD';
        }

        $val = $cond[1] ?? '';

        // Exists given `{key}` of `when:{key}={cond}` in `$data`
        // And `$data[{$key}]` == {cond}
        // And need to keep on validating (if has more validations)
        return (isset($data[$cond[0]]) && ($data[$cond[0]] == $val))
        ? 1

        // No given `{key}` of `when:{key}={cond}` in `$data`
        // Or `$data[{$key}]` != {cond}
        // And doesn't need to keep on validating
        : -1;
    }

    // Don't need start and end part
    public function regex(string $value, string $regex)
    {
        $regex = "/{$regex}/u";
        
        return (0 < preg_match($regex, $value));
    }

    public function domain($value)
    {
        return (
            (false !== filter_var($value, FILTER_VALIDATE_DOMAIN))
            // && ($value !== gethostbyname($value))
        );
    }

    public function ip($value)
    {
        return (false !== filter_var($value, FILTER_VALIDATE_IP));
    }

    public function host($value)
    {
        return ($this->ip($value) || $this->domain($value));
    }

    public function url($value)
    {
        return (false !== filter_var($value, FILTER_VALIDATE_URL));
    }
}
