Basic database query builder examples:

- Get database operate object:

``` php
// Default connection
$db = db();

// Specific a connection
$db = db('local_sqlite');
```

- SELECT

``` php
db()
->table('lif', 'a')
->leftJoin('lif b', 'a.id', '=', 'b.id')
->select('a.id aid, b.id bid')
->where('id', '>', 4)
->whereVal('like', '1%')
->whereAidBid(1, 2)
->or(function ($table) {
    $table->where('id', '<', 3)
    ->orValBid('lif', 2);
})
->sort([
    'aid asc',
    'bid' => 'desc',
])
// ->sort('aid asc', 'bid desc')
->group('aid', 'bid')
->limit(1)
->get();
```

- Insert

``` php
db()->table('lif')->insert([
    'id' => 1,
    'val' => 2,
]);

db()->table('lif')->insert([
    ['id' => 1,  'val' => '2'],
    ['id' => 11, 'val' => '22'],
]);
```

- Update

``` php
db()->table('lif')->whereId(1)->update([
    'val' => 'lif',
]);
```

- Delete

``` php
db()->table('lif')->whereId(1)->delete();
```

- Trasaction

``` php
db()->trans(function ($table) {
    $table->table('a')->whereId(1)->update(['val' => 2]);
    $table->table('b')->whereId(2)->delete();
});

// Or
db()->start();

if (...) {
    db()->commit();
} else {
    db()->rollback();   
}
```

- Truncate

``` php
db()->table('cache')->truncate();
```

- RAW

``` php
db()->raw('show variables like ?', ['ver%']);
```

- GET RAW SQL

``` php
db()
// ...
->sql();    // Without binding values
// ->_sql();    // With binding values

// Or
db()->table('lif')->whereId(1)->get(false, 1);    // Without binding values
db()->table('lif')->whereId(1)->get(false, 2);    // With binding values
```
