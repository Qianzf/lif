<?php

// -----------------------------------------------------
//     This is default route file of LiF
//     Use pseudo variable `$this` to register route
//     (Route groups needn't `use ($app)` any more)
// -----------------------------------------------------

$this->get('/', 'lif');

$this->get('test', function () {
    $schema = new \Lif\Core\Storage\SQL\Schema;

    $schema->dropIfExists('test');

    // $schema->table('user')->comment('User Table');

    // $schema->createIfNotExists('test', function ($table) {
    //     $table->pk('id', 'bigint', 20);
    //     $table->medint('key1');
    //     $table->tinyint('key2');
    //     $table->double('key3');
    //     $table->numeric('key4');
    //     $table->char('title', 128)->comment('Title');
    //     $table->string('name', 64)->comment('Name');
    //     $table->blob('data', 1024)->comment('Binary data');
    //     $table->enum('status', 1, 2, 3)->comment('Enum');
    //     $table->set('type', 4, 5, 6)->comment('Set');

    //     $table->autoincre(1)->comment('This is table comment');
    // });

    // $schema->alter('tmp2', function ($table) {
    //     $table->renameTableAs('tmp3');
    // });die;

    // dd($schema->table('tmp1')->renameTo('test'));

    // $schema->table('test')->renameTo('tmp1');die;

    dd($schema->table('demo')->rename('test'));die;
    dd($schema->table('test')->dropColDefault('key2'));die;
    dd($schema->table('test')->dropCol('key4'));die;

    $res = $schema
    ->table('user')
    ->addCol('group')
    // ->first()
    // ->after('role')
    ->tinyint();
    // ->default(1)
    // ->comment('User group ID');

    dd($res);
});
