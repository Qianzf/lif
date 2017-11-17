### Schema class

``` php
$schema = new \Lif\Core\Storage\SQL\Schema;
schema();

// Scheming given database connection
$scheme->setConn('mysql_rw_2');
schema()->setConn('mysql_rw_2');
schema('mysql_rw_2');
```

### Use database 

``` php
// Use database `lif` on connection `mysql57`
schema('mysql57')->useDB('lif');
```

### Check database 

``` php
// Check if database `abcd` exists on connection `mysql57`
schema('mysql57')->hasDB('abcd');    // `true` or `false`
// Check if database is in using on connection `mysql57`
schema('mysql57')->hasDBUsed();    // bool `false` or string database name
schema('mysql57')->useDB('lif')->hasDBUsed();
```

### Create database

- Create database use default charset and collate settings

``` php
$schema->createDB('test');
$schema->createDBIfNotExists('test');
$schema->createDB('test', true);    // check if not exists
```

- Create database use custom charset and collate settings

``` php
$schema->createDBIfNotExists('test', function ($db) {
    $db->charset('utf8m4')->collate('utf8m4_unicode_ci');
});
```

### Drop database

```php
$schema->dropDB('test');
$schema->dropDBIfExists('test');
$schema->dropDB('test', true);    // check if exists
```

#### Check table

``` php
schema('mysql57')->hasTable('db');    // `true` or `false`
schema('mysql57')->useDB('mysql')->hasTable('db');
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

#### Default with raw SQL grammer

``` php
schema()->createIfNotExists('user', function ($table) {
    $table->pk('id');

    $table
    ->datetime('create_at')
    ->default('CURRENT_TIMESTAMP()', true);

    // Or
    $table
    ->datetime('create_at')
    ->default(function () {
        return 'CURRENT_TIMESTAMP()';
    });
});
```

### Alter table

``` php
// In Closure
$schema->alter('user', function ($table) {
    // Drop column
    $table
    ->drop('group');

    // Add column
    $table
    ->add('group')
    //->first()
    ->after('role')
    ->tinyint()
    ->nullable()
    ->comment('User group ID');

    // Modfiy column
    $table
    ->modify('email')
    ->first()
    ->char(128)
    //->after('role')
    ->unique();

    // Change column
    $table
    ->change('group', 'group2')
    ->after('name')
    ->tinyint();

    // Set column default
    $table->setDefault('group', 1);
    // Drop column default
    $table->dropDefault('group');

    // Rename table
    $table->renameTableTo('user2');
    $table->renameTableAs('user3');
    $table->renameTable('user4', 'TO');
    $table->renameTable('user5', 'AS');
    $table->renameTable('user6');    // TO
});

// Or, identically
// Drop column
$schema->table('user')->dropCol('group');
$schema->table('user')->dropColumn('group');

// Add column
$schema
->table('user')
->addCol('group')
// ->addColumn('group')
->after('role')
->tinyint()
->default(1)
->comment('User group ID');

// Modfiy column
$schema
->table('user')
->modifyCol('email')
// ->modifyColumn('email')
->first()
->char(128)
//->after('role')
->unique();

// Change column
$schema
->table('user')
->changeCol('group', 'group2')
// ->changeColumn('group', 'group2')
->after('name')
->tinyint();

// Set column default
$schema->table('user')->setColDefault('group', 1);
$schema->table('user')->setColumnDefault('group', 1);
// Drop column default
$schema->table('user')->dropColDefault('group');
$schema->table('user')->dropColumnDefault('group');

// Rename table
$schema->table('user')->renameTo('tmp1');
$schema->table('tmp1')->renameAs('tmp2');
$schema->table('tmp2')->rename('tmp3', 'AS');
$schema->table('tmp3')->rename('tmp4', 'TO');
$schema->table('tmp4')->rename('user');    // TO
```

### Drop table

``` php
$schema->drop('table1', 'table2', ...);
$schema->dropIfExists('table1', 'table2', ...);
```

### Table settings
``` php
$schema->table('table1')->comment('This is the comment of table1');
$schema->comment('table1', 'This is the comment of table1');

// This is the new auto_increment start of table2
$schema->table('table2')->autoincre(1024);
$schema->autoincre('table2', 1024);

$schema->table('table3')->engine('InnoDB');
$schema->engine('table3', 'InnoDB');

$schema->table('table4')->charset('utf8m4');
$schema->charset('table4', 'utf8m4');

$schema->table('table5')->collate('utf8_unicode_ci');
$schema->collate('table5', 'utf8_unicode_ci');
```

> Due to specific database limitations, some schema operation will not make effect, like mysql trasaction implict commit staff.
> 
> If these things happened, check out the raw sql in database client first.

### Commit schema

By default, schema definitions will be auto commited if you don't do anything after schema definitionings.

However, you can also commit schema definitions (immediately) manually by `$schema->commit()`.

