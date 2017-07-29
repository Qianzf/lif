<?php

namespace Lif\Core\Storage;

class Db
{
    public $pdo   = null;
    public $new   = null;
    public $tbPre = '';

    public function __construct($new = false)
    {
        if (!$this->pdo || !is_object($this->pdo)) {
            $this->new = $new;
            $this->pdo = $this->getInstance();
        }
    }

    public function getInstance()
    {
        if ($this->new || !$this->pdo || !is_object($this->pdo)) {
            try {
                $db = config('db')['pdo'];
                return new \PDO(
                    $db['dsn'],
                    $db['user'],
                    $db['passwd']
                );
            } catch (\PDOException $pdoE) {
                exception($pdoE);
            }
        } else {
            return $this->pdo;
        }
    }
}
