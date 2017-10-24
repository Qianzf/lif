<?php

// ---------------------------------
//     LiF worker job base class
// ---------------------------------

namespace Lif\Core\Abst;

use \Lif\Core\Intf\Job as JobContract;

abstract class Job implements JobContract
{
    protected $detail = [];

    public function __construct(array $detail = [])
    {
        $this->detail = $detail;
    }

    public function get(string $key)
    {
        return $this->$detail[$key] ?? null;
    }

    public function set(string $key, $data) : void
    {
        $this->$detail[$key] = $data;
    }

    public function run()
    {
    }
}
