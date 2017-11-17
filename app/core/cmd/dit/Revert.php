<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Revert extends Command
{
    protected $intro  = 'Revert dits to a given version';
    protected $option = [
        '--ver' => 'setVersion',
        '--id'  => 'setID',
    ];
    protected $desc   = [
        'setVersion' => 'Specific the version to revert, default is last version.',
        'setID' => 'Specific the dit ID to revert'
    ];

    private $ditVersion = null;
    private $ditID = null;

    public function fire()
    {
        init_dit_table();
        
        $revertToLast = false;
        // Check if revert version exists
        if (is_null($this->ditVersion)) {
            $query = db()
            ->table('__dit__')
            ->select('distinct `version`')
            ->sort([
                'version' => 'desc',
            ])
            ->limit(2);

            if ($this->ditID) {
                $query = $query->whereId($this->ditID);
            }

            $versions = $query->get();

            $this->ditVersion = $versions[1]['version'] ?? null;
            if (! $this->ditVersion) {
                $this->ditVersion = $versions[0]['version'] ?? null;
                if ($this->ditVersion) {
                    $this->ditVersion = intval($this->ditVersion);
                    $revertToLast = true;
                }
            }

            if (! $this->ditVersion) {
                $this->nothingHappened();
            }
        }

        if (0 !== $this->ditVersion) {
            $hasVersion = db()
            ->table('__dit__')
            ->whereVersion($this->ditVersion)
            ->count();

            if (! $hasVersion) {
                $this->fails("Dits version `{$this->ditVersion}` not found.");
            }
        }

        if ($revertToLast) {
            --$this->ditVersion;
        }

        // Find the dits class to be reverted
        $query = db()
        ->table('__dit__')
        ->select('id', 'name')
        ->whereVersion('>', $this->ditVersion);

        // Check if dit ID exists
        if ($this->ditID) {
            $query = $query->whereId($this->ditID);
        }
        
        if (! ($_dits = $query->get())) {
            $this->nothingHappened();
        }

        $dits = array_column($_dits, 'name');
        $ids  = $this->ditID ? $this->ditID : array_column($_dits, 'id');
        $text = $this->ditID ?? implode(',', $ids);

        // Revert dits
        foreach ($dits as $dit) {
            $ns = nsOf('dbvc', $dit, false);
            if (class_exists($ns)) {
                if (($_dit = (new $ns)) instanceof \Lif\Core\Storage\Dit) {
                    $_dit->revert();
                }
            }
        }

        // Delete commited dits whose version greater to revert version
        $deleted = db()
        ->table('__dit__')
        ->whereVersion('>', $this->ditVersion)
        ->whereId($ids)
        ->delete();

        if ($deleted >= 0) {
            $this->success(
                "Dits [{$text}] has reverted to verion: "
                .($revertToLast ? '(last)' : $this->ditVersion)
            );
        } else {
            $this->fails(
                "Reverting dits [{$text}] to version {$this->ditVersion} failed."
            );
        }
    }

    public function setID($id = null)
    {
        if ($id && (!is_numeric($id)
            || ($id != ($_id = intval($id)))
            || ($_id < 0)
        )) {
            $this->fails('Dit ID must be an positive integer.');
        }

        $this->ditID = $_id;
    }

    public function setVersion($version = null)
    {
        if ($version && (!is_numeric($version)
            || ($version != ($_version = intval($version)))
            || ($_version < 0)
        )) {
            $this->fails('Dit version must be an positive integer.');
        }

        $this->ditVersion = $_version;
    }

    private function nothingHappened()
    {
        $this->info(
            'No dits can revert to version '
            .(empty_safe($this->ditVersion) ? '(last)' : $this->ditVersion)
            .', nothing happened.'
        );
    }
}
