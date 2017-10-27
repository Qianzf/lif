#### Queue config

``` php
return [
    'type'  => 'db',
    'conn'  => 'local_sqlite',
    'table' => 'job',
    'defs'  => [
        // 'id',
        // 'queue',
        // 'detail',
        // 'tried',
        // 'create_at',
        // 'finish_at',
        // 'restart',
        // 'retry',
    ],
];    
```

If `defs` is unset or empty, then will use `default_queue_defs_get()` instead.

#### Job enqueue

``` php
class TestJob extends \Lif\Job\Job
{
    public funciton run() : bool
    {
        // do sth...
    }
}
class TestCtl extends Ctl
{
    use \Lif\Core\Traits\Queue;

    public function test()
    {
        $this->enqueue(new TestJob)
        
        // Specific queue name
        ->on('test_queue')

        // Setting max try times
        ->try(2)

        // Setting max execution time (seconds)
        ->timeout(60);
    }
}
```

#### Listening queue

``` shell
php lif queue.run -N test,demo,foo
```

#### Restart failed jobs

``` shell
php lif queue.restart --name=test,demo,bar
```

###### Notices

- When using SQLite as queue Medium
 
SQLite is not used for many concurrent writers scenario.

> [Appropriate Uses For SQLite](https://sqlite.org/whentouse.html)

Since `queue.restart` and `queue.run` are writing into SQLite at the same time, so when queue medium is SQLite, `queue.restart` will not actully effect unless you stop queue worker manully first and `queue.restart` and re-run `queue.run`.
