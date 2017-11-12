### Schema class

``` php
$schema = new \Lif\Core\Storage\SQL\Schema;
```

### Create table

``` php
// 1. create whenever
$schema->create('table', function ($table) {
    $table->pk('id', 'bigint', 20);
    $table->medint('key1');
    $table->tinyint('key2');
    $table->double('key3');
    $table->numeric('key4');
    $table->char('title', 128)->comment('Title');
    $table->string('name', 64)->comment('Name');
    $table->blob('data', 1024)->comment('Binary data');
    $table->enum('status', 1, 2, 3)->comment('Enum');
    $table->set('type', 4, 5, 6)->comment('Set');
    $table->json('extra')->comment('extra data');    // MySQL 5.7+

    $table->autoincre(1)->comment('This is table comment');
});

// 2. create if not exists
$schema->createIfNotExists('table', function ($table) {
    // definitions...
}
```

### Alter table

``` php
$schema->alter('user', function ($table) {
    // Drop column
    $table
    ->dropColumn('group');

    // Add column
    $table
    ->addColumn('group')
    //->first()
    ->after('role')
    ->tinyint()
    ->nullable()
    ->comment('User group ID');

    // Modfiy column
    $table
    ->modifyColumn('email')
    ->first()
    ->char(128)
    //->after('role')
    ->unique();
});
```

### Drop table

``` php
$schema->drop('table1', 'table2', ...);
$schema->dropIfExists('table1', 'table2', ...);
```

### Table settings
``` php
$schema->comment('table1', 'This is the comment of table1');

// This is the new auto_increment start of table2
$schema->autoincre('table2', 1024);

$schema->engine('table3', 'InnoDB');

$schema->charset('table4', 'utf8m4');

$schema->collate('table5', 'utf8_unicode_ci');
```

### Commit schema

By default, schema definitions will be auto commited if you don't do anything after schema definitionings.

However, you can also commit schema definitions manually by `$schema->commit()``.
