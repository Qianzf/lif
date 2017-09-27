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

    protected function path()
    {
        return pathOf('sysmsg').$this->lang;
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
        if (! $this->lang) {
            $this->lang = $lang ?? $this->getDefaultLang();
        }

        $dat = [];

        if ($fsi = $this->getFilesystemIterator()) {
            foreach ($fsi as $file) {
                if ($file->isFile() && 'php' == $file->getExtension()) {
                    $_dat = include_once $file->getPathname();
                    if ($_dat && is_array($_dat)) {
                        $dat = array_merge($_dat, $dat);
                    }
                }
            }
        }

        return $this->text = $dat;
    }

    protected function getDefaultLang(): string
    {
        return 'zh';
    }

    protected function getFilesystemIterator()
    {
        if (($path = $this->path())) {
            if (! file_exists($path)) {
                $this->lang = 'zh';
                $path = $this->path();
                if (! file_exists($path)) {
                    return false;
                }
            }

            return new \FilesystemIterator($path);
        }

        return false;
    }
}
