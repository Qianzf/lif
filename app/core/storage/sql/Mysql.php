<?php

// ----------------------------
//     MySQL schema builder
// ----------------------------

namespace Lif\Core\Storage\SQL;

use Lif\Core\Storage\SQL\Mysql\Table;

class Mysql
{
    // @return fluent SQL
    public function __call($name, $params)
    {
        return call_user_func_array(
            [(new Table), $name],
            $params
        );
    }
}
