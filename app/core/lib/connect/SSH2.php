<?php

namespace Lif\Core\Lib\Connect;

final class SSH2
{
    private $host = null;
    private $port = null;
    private $user = null;
    private $conn = null;
    private $pswd = null;
    private $pubk = null;
    private $prik = null;
    private $auth = false;

    public function __construct(
        string $host = null,
        int $port = 22,
        string $user = 'root'
    ) {
        $this
        ->prepare()
        ->setHost($host)
        ->setPort($port)
        ->setUser($user);
    }

    public function prepare() : SSH2
    {
        error_reporting(0);

        if (! extension_loaded('ssh2')) {
            excp('PHP extension ssh2 not installed.');
        }

        return $this;
    }

    public function setHost(string $host) : SSH2
    {
        if (!($_host = gethostbyname($host)) || ($_host === $host)) {
            excp('Unreachable server host: '.($host ? $host : '<empty>'));
        }

        $this->host = $host;

        return $this;
    }

    public function setPort(int $port) : SSH2
    {
        $this->port = $port;

        return $this;
    }

    public function setUser(string $user) : SSH2
    {
        $this->user = $user;

        return $this;
    }

    public function setPasswd(string $pswd) : SSH2
    {
        $this->pswd = $pswd;

        return $this;
    }

    public function setPubkey(string $pubk) : SSH2
    {
        $this->pubk = $pubk;
        
        return $this;
    }

    public function setPrikey(string $prik) : SSH2
    {
        $this->prik = $prik;
        
        return $this;
    }

    public function auth() : bool
    {
        if ($this->paswd) {
            return (bool) ssh2_auth_password(
                $this->conn,
                $this->user,
                $this->pswd
            );
        }


        if ($this->pubk && $this->prik) {
            return (bool) ssh2_auth_pubkey_file(
                $this->conn,
                $this->user,
                $this->pubk,
                $this->prik
            );
        }

        return false;
    }

    public function connect(array $options = []) : SSH2
    {
        if (true !== ($err = $this->canConnect())) {
            excp(
                'Connection can not established, missing identity items: '
                .$err
            );
        }

        $this->conn = ssh2_connect(
            $this->host,
            $this->port,
            $options
        );

        if (! ($this->auth = $this->auth())) {
            excp('Connection auth failed.');
        }

        return $this;
    }

    public function exec($cmds)
    {
        if (!$this->conn || !$this->auth) {
            return [
                'num' => -1,
                'err' => null,
                'out' => null,
            ];
        }

        $_cmds = build_cmds($cmds).';echo -ne "#SSH2_EXEC_EXIT_STATUS#$?"';

        $streamOut = ssh2_exec($this->conn, $_cmds);
        $streamErr = ssh2_fetch_stream($streamOut, SSH2_STREAM_STDERR);
        stream_set_blocking($streamOut, true);
        stream_set_blocking($streamErr, true);
        $stdout    = stream_get_contents($streamOut);
        $stderr    = stream_get_contents($streamErr);
        $status    = 0;

        $stdout = preg_replace_callback(
            '/\#SSH2_EXEC_EXIT_STATUS\#(\d+)/u',
            function ($matches) use (&$status) {
                $status = intval($matches[1] ?? -1);
                return '';
        }, $stdout);
        fclose($streamOut);
        fclose($streamErr);

        return [
            'num' => $status,
            'err' => $stderr,
            'out' => $stdout,
        ];
    }

    public function getConnect()
    {
        return $this->conn;
    }

    public function canConnect()
    {
        if (! $this->user) {
            return 'User';
        }

        if ($this->pswd || ($this->pubk && $this->prik)) {
            return true;
        }

        return 'Password or public key';
    }

    public function __destruct()
    {
        error_reporting('E_ALL');
    }
}
