<?php

// ----------------------------------
//     LiF Web Session management
// ----------------------------------

namespace Lif\Core\Web;

class Session
{
    public function __construct()
    {
        if (! session_id()) {
            session_start();
        }
    }

    public function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function destory()
    {
        session_destroy();
        setcookie('LIFSESSID', '', time()-1);
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function flush($key)
    {
        $value = $this->get($key);
        $this->delete($key);

        return $value;
    }
}
