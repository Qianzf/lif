<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateAcceptanceTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('acceptance', function ($table) {
            $table->pk('id');

            $table
            ->char('whose', 8)
            ->default('story')
            ->comment('Acceptances of what: story/bug');

            $table
            ->int('origin')
            ->unsigned()
            ->comment('Acceptances owner id');

            $table
            ->bigint('version')
            ->unsigned()
            ->default(0)
            ->comment('Related origin version ID');

            $table
            ->text('detail')
            ->comment('Acceptance detail');

            $table
            ->char('status', 16)
            ->nullable()
            ->comment('Acceptances status: `NULL`/checked');

            $table
            ->comment('Acceptance criteria table');
        });
    }

    public function revert()
    {
        schema()->dropIfExists('acceptance');
    }
}
