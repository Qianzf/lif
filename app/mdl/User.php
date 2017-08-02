<?php

namespace Lif\Mdl;

class User
{
    protected $id = null;

    public function __construct($id = null)
    {
        $this->id = $id ? $id : 1024;
    }

    public function id()
    {
        return $this->id;
    }
}
