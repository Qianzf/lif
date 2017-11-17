<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Commit extends Command
{
    protected $intro = 'Commit new-added dits';

    public function fire()
    {
        init_dit_table();

        $dits = db()
        ->table('__dit__')
        ->select('name', 'version')
        ->sort([
            'version' => 'desc'
        ])
        ->get();

        $committed = array_column($dits, 'name');
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

                    try {
                        db()->start();

                        (new $ns)->commit();

                        db()->table('__dit__')->insert([
                            'name'    => $dit,
                            'version' => ++$version,
                        ]);

                        db()->commit();
                        $this->success("{$output} (Success)", false);
                    } catch (\PDOException $pdoe) {
                        db()->rollback();
                        $this->fails("{$output (failed)}");
                    }
                } else {
                    $this->info("{$output} (Skipped)", false);
                }
            }
        );
    }
}
