<?php

// ----------------------------------------
//     LiF command job public interface
// ----------------------------------------

namespace Lif\Core\Intf;

interface Job
{
    public function __construct(array $data = []);

    public function get(string $key);

    public function set(string $key, $data) : void;

    public function run() : bool;
}
