<?php

// ---------------------------------
//     Version controll database
// ---------------------------------

namespace Lif\Core\Intf;

interface VCD extends DBConn
{
    public function commit();

    public function revert();

    public function execute();
}
