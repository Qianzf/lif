<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    $servers = db()->table('server')->get();
    
    foreach ($servers as $server) {
        $ssh2 = new \Lif\Core\Lib\Connect\SSH2($server['host']);

        $ssh2 = $ssh2
        ->setPubkey($server['pubk'])
        ->setPrikey($server['prik'])
        ->connect([
            'hostkey' => 'ssh-rsa',
        ]);

        pr($server['name'], $ssh2->exec([
            'cd /data/wwwroot',
            'ls -lh',
        ]));
    }
});
