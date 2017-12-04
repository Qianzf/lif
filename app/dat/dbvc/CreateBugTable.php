<?php

namespace Lif\Dat\Dbvc;

use Lif\Core\Storage\Dit;

class CreateBugTable extends Dit
{
    public function commit()
    {
        schema()->createIfNotExists('bug', function ($table) {
            $table->pk('id');

            $table->string('title');

            $table
            ->text('how')
            ->comment('What are u doing when bug occurs');

            $table
            ->text('what')
            ->comment('What is your specific expectation');

            $table
            ->string('errmsg')
            ->nullable();

            $table
            ->string('errcode', 64)
            ->nullable();

            $table->char('os', 16);
            $table->string('os_ver', 32);
            $table->string('platform');
            $table->string('platform_ver', 32);

            $table
            ->char('recurable', 16)
            ->comment('Can this bug reproduce: everytime,accidental,no');

            $table
            ->text('extra')
            ->nullable()
            ->comment('Extra information of this bug');

            $table
            ->string('contact_name')
            ->nullable();

            $table
            ->string('contact_email')
            ->nullable();

            $table
            ->string('contact_phone')
            ->nullable();

            $table
            ->int('creator')
            ->unsigned()
            ->comment('User who created this bug => `user`.`id`');

            $table
            ->datetime('create_at')
            ->default('CURRENT_TIMESTAMP()', true);
        });
    }

    public function revert()
    {
        schema()->dropIfExists('bug');
    }
}
