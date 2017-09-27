<?php

namespace Lif\Core;

class Coll implements
\Iterator,
\ArrayAccess,
\Countable,
\JsonSerializable
{
    use \Lif\Core\Traits\MethodNotExists;

    private $origin  = null;
    private $data    = [];
    private $keys    = [];
    private $count   = -1;
    private $pointer = 0;

    public function __construct($data, $origin = null)
    {
        $this->origin = $origin;
        $this->data   = $data;
        $this->keys   = array_keys($data);
    }

    public function get($key)
    {
        return exists($this->data, $key)
        ? $this->data[$key] : null;
    }

    public function set($key, $value)
    {
        if ($key && is_string($key)) {
            $this->data[$key] = $value;
        } else {
            $this->data[] = $value;
        }
    }

    public function origin()
    {
        return $this->origin;
    }

    public function __call($name, $args)
    {
        if (is_object($this->origin)
            && method_exists($this->origin, $name)
        ) {
            return $this->origin->$name($args);
        }
    }

    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key();
        }

        return $this->get($key);
    }

    public function current()
    {
        return $this->data[$this->keys[$this->pointer]];
    }

    public function key()
    {
        return $this->keys[$this->pointer];
    }

    public function next()
    {
        ++ $this->pointer;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function valid()
    {
        return (
            isset($this->keys[$this->pointer])
            && isset($this->data[$this->keys[$this->pointer]])
        );
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        if (isset($this->data[$offset])) {
            unset($this->data[$offset]);
        }
    }

    public function count()
    {
        return (-1 !== $this->count)
        ? $this->count : count($this->data);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function toString()
    {
        return _json_encode($this->data);
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
