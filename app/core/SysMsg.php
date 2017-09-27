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

        if (! file_exists($path.$this->lang)) {
            $path .= $this->getDefaultLang();
        }

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
        return isset($this->text[$offset]) ?? $offset;
    }

    public function offsetSet($offset, $value): void
    {
    }

    public function offsetUnset($offset): void
    {
    }

    public function msg($lang): self
    {
        $this->load($lang);

        return $this;
    }

    public function load($lang = null)
    {
        if (! $lang) {
            $this->lang = $this->lang ?? $this->getDefaultLang();
        }

        return $this->text = load_array($this->path());
    }

    protected function getDefaultLang(): string
    {
        return 'zh';
    }

    protected function getFilesystemIterator($path = null)
    {
        $path = $path ?? $this->path();

        if (! file_exists($path)) {
            return false;
        }

        return new \FilesystemIterator($path);
    }
}
