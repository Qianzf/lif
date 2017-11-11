<?php

namespace Lif\Core\Storage;

class Dit implements \Lif\Core\Intf\VCD
{
    use \Lif\Core\Traits\WithDB;

    public function commit()
    {
    }

    public function revert()
    {       
    }

    public function execute()
    {
        dd($this->db());
    }
}
