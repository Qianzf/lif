<?php

namespace Lif\Core\Intf;

interface Observable
{
    public function addObserver($observer);
    public function name();
}
