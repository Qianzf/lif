<?php

namespace Lif\Core;

class DB
{
    public $pdo   = null;
    public $tbPre = '';
    public $config = [];

    public function __construct($config, $forceNew = false)
    {
        $this->config = $config;

        if (!$this->pdo || !is_object($this->pdo)) {
            $this->pdo = $this->getDBInstance($forceNew);
        }
    }

    public function getDBInstance($forceNew = false)
    {
        if ($forceNew || !$this->pdo || !is_object($this->pdo)) {
            try {
                return new \PDO(
                    $this->config['dsn'],
                    $this->config['user'],
                    $this->config['passwd']
                );
            } catch (PDOException $pdoE) {
                exit($pdoE->getMessage());
            }
        } else {
            return $this->pdo;
        }
    }
}
