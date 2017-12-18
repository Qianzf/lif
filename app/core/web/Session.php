<?php

// ----------------------------------
//     LiF Web Session management
// ----------------------------------

namespace Lif\Core\Web;

class Session
{
    private $data = [];

    public function __construct()
    {
        if (context('web') && !session_id()) {
            if (headers_sent()) {
                excp(
                    'Session starting failed: HTTP headers sent already.'
                );
            }

            session_start();
        }

        $this->data = $_SESSION ?? [];
    }

    public function set($key, $val)
    {
        if (false === mb_strpos($key, '.')) {
            $this->data[$key] = $val;
        } else {
            $this->data = array_update_by_coherent_keys($key, $this->data, $val);
        }

        return true;
    }

    public function sets(array $data = [])
    {
        if ($data) {
            foreach ($data as $key => $val) {
                $this->set($key, $val);
            }
        }
    }

    public function get($key)
    {
        return array_query_by_coherent_keys($this->data, $key);
    }

    public function all()
    {
        return $this->data;
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
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }

        return true;
    }

    public function flush($key)
    {
        $value = $this->get($key);
        
        $this->delete($key);

        return $value;
    }

    public function update()
    {
        if (context('web') && headers_sent()) {
            excp(
                'Update session failed: HTTP headers sent already.'
            );
        }

        session_regenerate_id();
    }
}
