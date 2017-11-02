<?php

namespace Lif\Core\Logger;

class File extends \Lif\Core\Abst\Logger
{
    public function validate() : bool
    {
        legal_or($this->config, [
            'path' => ['string', 'lif.log']
        ]);

        return true;
    }

    public function write()
    {
        $path = pathOf('log', $this->config['path']);
        
        if ($this->data) {
            put2file($path, $this->data);
        }
    }

    public function setPath(string $path) : File
    {
        $this->config['path'] = $path;

        return $this;
    }

    public function getDriver()
    {
        return $this->config['driver'] ?? 'file';
    }
}
