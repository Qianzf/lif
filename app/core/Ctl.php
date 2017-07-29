<?php

namespace Lif\Core;

class Ctl
{
    protected $app = null;

    public function __call($name, $args)
    {
        if ('__NON_EXISTENT_METHOD__' === $name) {
            if (!isset($args[0]) || !is_object($args[0])) {
                api_exception(
                    'Missing strategy object in params pass to controller.'
                );
            }
            if (!isset($args[1]) || !$args[1] || !is_string($args[1])) {
                api_exception(
                    'Missing action in params pass to controller.'
                );
            }
            $this->app = $args[0];
            $this->{$args[1]}();
        } else {
            api_exception(
                'Method not exists.'
            );
        }
    }
}
