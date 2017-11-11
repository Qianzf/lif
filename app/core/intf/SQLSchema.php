<?php

namespace Lif\Core\Intf;

interface SQLSchema
{
    public function create(string $table, \Closure $ddl);
}
