<?php

// -----------------------
//     System messages
// -----------------------

namespace Lif\Core;

use Lif\Core\Web\Request;

class SysMsg implements \ArrayAccess
{
    public $req  = null;
    public $lang = null;
    public $text = [];

    protected function path() : string
    {
        $path = pathOf('sysmsg');

        $path .= file_exists($path.$this->lang)
        ? $this->lang
        : $this->getDefaultLang();

        return $path;
    }

    protected function request()
    {
        if (!$this->req && $this->req instanceof Request) {
            return $this->req;
        }

        return new Request;
    }

    public function get()
    {
        $this->req = $this->request();

        $this->lang = $this->req->get('lang') ?? $this->getDefaultLang();

        return response($this->load());
    }

    public function offsetExists($offset)
    {
        return isset($this->text[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->text[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->text[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->text[$offset]);
    }

    public function msg($lang): self
    {
        $this->load($lang);

        return $this;
    }

    public function load($lang = null)
    {
        $this->lang = $lang ?? ($this->lang ?? $this->getDefaultLang());

        return $this->text = load_array($this->path());
    }

    protected function getDefaultLang(): string
    {
        return 'zh';
    }
}
