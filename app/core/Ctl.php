<?php

namespace Lif\Core;

use Lif\Core\Abst\Container;

class Ctl extends Container
{
    public function __call($name, $args)
    {
        if ('__NON_EXISTENT_METHOD__' === $name) {
            if (!isset($args[0]) || !is_object($args[0])) {
                excp(
                    'Missing strategy object in params pass to controller.'
                );
            }
            if (!isset($args[1]) || !$args[1] || !is_string($args[1])) {
                excp(
                    'Missing action in params pass to controller.'
                );
            }
            $this->app = $args[0];
            $this->{$args[1]}();
        } else {
            excp(
                'Method `'.$name.'()` of `'.(static::class).'` not exists.'
            );
        }
    }
}
