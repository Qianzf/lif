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
