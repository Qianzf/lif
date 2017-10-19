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
            if (headers_sent()) {
                excp(
                    'Session set failed coz HTTP header has been sent.'
                );
            }
            session_start();
        }
    }

    public function set($key, $val)
    {
        if (false === mb_strpos($key, '.')) {
            $_SESSION[$key] = $val;
        } else {
            $_SESSION = array_update_by_coherent_keys($key, $_SESSION, $val);
        }

        return true;
    }

    public function get($key)
    {
        return array_query_by_coherent_keys($_SESSION, $key);
    }

    public function all()
    {
        return $_SESSION;
    }

    public function destory()
    {
        // session_regenerate_id(true);
        return (
            session_destroy()
            && setcookie('LIFSESSID', '', time()-1)
        );
    }

    public function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }

        return true;
    }

    public function flush($key)
    {
        $value = $this->get($key);
        
        $this->delete($key);

        return $value;
    }
}
