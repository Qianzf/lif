<?php

namespace Lif\Core\Traits;

trait Ditable
{
    public function commit(string $type = null)
    {
        $dits = db()
        ->table('__dit__')
        ->select('name', 'version')
        ->sort([
            'version' => 'desc'
        ])
        ->get();

        $committed = array_column($dits, 'name');
        $version   = intval($dits[0]['version'] ?? 0);

        $types = ['dbvc', 'dbseed'];
        // !!! Make sure Same commit same version number
        if (!is_null($type) && in_array($type, $types)) {
            $types = [$type];
        }

        foreach ($types as $_type) {
            $this->__commit($committed, $version, $_type);
        }
    }

    public function __commit(array $committed, int $version, string $type)
    {
        load_object(pathOf($type),
            function (string $dit) use ($committed, $version, $type)
            {
                $output = "Ditting: `{$dit}` ...";

                if (! in_array($dit, $committed)) {
                    $ns = nsOf($type, $dit, false);

                    if (! class_exists($ns)) {
                        excp('Dit class not exists: '.$ns);
                    }

                    try {
                        db()->start();

                        (new $ns)->commit();

                        db()->table('__dit__')->insert([
                            'name'    => $dit,
                            'type'    => $type,
                            'version' => ++$version,
                        ]);

                        db()->commit();
                        
                        return $this->success("{$output} (Success)", false);
                    } catch (\Error $e) {
                    } catch (\PDOException $pdoe) {
                    } finally {
                    }

                    db()->rollback();
                    return $this->fails("{$output (failed)}");
                } else {
                    $this->info("{$output} (Skipped)", false);
                }
            }
        );
    }
}
