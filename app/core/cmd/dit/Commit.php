<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Commit extends Command
{
    protected $intro = 'Commit new-added dits';

    public function fire()
    {
        $dits      = db()
        ->table('lif_dit')
        ->select('dit', 'version')
        ->sort([
            'version' => 'desc'
        ])
        ->get();

        $committed = array_column($dits, 'dit');
        $version   = intval($dits[0]['version'] ?? 0);

        load_object(pathOf('dbvc'),
            function (string $dit) use ($committed, $version)
            {
                $output = "Ditting: `{$dit}` ...";

                if (! in_array($dit, $committed)) {
                    $ns = nsOf('dbvc', $dit, false);

                    if (! class_exists($ns)) {
                        excp('Dit class not exists: '.$ns);
                    }

                    (new $ns)->commit();

                    db()->table('lif_dit')->insert([
                        'dit'     => $dit,
                        'version' => ++$version,
                    ]);

                    $this->success("{$output} (Success)", false);
                } else {
                    $this->info("{$output} (Skipped)", false);
                }
            }
        );
    }
}
