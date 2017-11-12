<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    $schema = new \Lif\Core\Storage\SQL\Schema;

    // $schema->dropIfExists('test');

    // $schema->createIfNotExists('test', function ($table) {
        // $table->pk('id', 'bigint', 20);
        // $table->medint('key1');
        // $table->tinyint('key2');
        // $table->double('key3');
        // $table->numeric('key4');
        // $table->char('title', 128)->comment('Title');
        // $table->string('name', 64)->comment('Name');
        // $table->blob('data', 1024)->comment('Binary data');
        // $table->enum('status', 1, 2, 3)->comment('Enum');
        // $table->set('type', 4, 5, 6)->comment('Set');

        // $table->comment('This is table comment');
    // });

    $schema->alter('user', function ($table) {
        // $table
        // ->dropColumn('group');

        // $table
        // ->addColumn('group')
        // ->after('role')
        // ->tinyint()
        // ->nullable()
        // ->comment('User group ID');

        $table
        ->modifyColumn('email')
        // ->first()
        ->char(128)
        ->after('role')
        ->unique();
    });
});
