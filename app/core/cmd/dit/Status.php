<?php

namespace Lif\Core\Cmd\Dit;

use Lif\Core\Abst\Command;

class Status extends Command
{
    protected $intro = 'List current dits status';

    public function fire()
    {
        $commited = db()->table('lif_dit')->get();

        $table = '-- Commited Dits --';
        $title = 'ID | Name | Version | Create At';
        $this->info("{$table}\n{$title}", false);

        foreach ($commited as $dit) {
            $column = implode(' | ', $dit);

            $this->success($column, false);
        }

        $_table = '-- Uncommited Dits --';
        $_title = 'Name';
        $this->info("{$_table}\n{$_title}", false);

        load_object(pathOf('dbvc'), function ($dit) use ($commited) {
            $_dits = array_column($commited, 'dit');
            if (! in_array($dit, $_dits)) {
                $this->fails("- $dit", false);
            }
        });
    }
}
