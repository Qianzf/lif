<?php

namespace Lif\Mdl;

class User
{
    protected $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }
}
