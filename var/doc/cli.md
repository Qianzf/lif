## Basic usage

``` shell
php lif command [options] [parameters]

# Examples:

php lif user.email.notify --debug --query is_new_user=true
```

## With options

``` shell
php lif -V
php lif -vvv
php lif --list
php lif --list-all
php lif --list-core
php lif --list-user
php lif --cmds
php lif --cmds=all
php lif --cmds=core
php lif --cmds=user
php lif -L
php lif -L=core
php lif -L=user
# ...
```

## With parameters

- JSON

``` shell
php lif test.cmd --json-file p.json
php lif test.cmd --json {"a":"1"}
```

- XML

``` shell
php lif route.cache.clear --xml-file p.xml
php lif route.cache.clear --xml-file '<xml><a>1</a></xml>'
```

- URL query string

``` shell
php lif view.cache.clear --query-file /path/to/file
php lif view.cache.clear --query 'a=1&b=2'
```

## Notice

- Core command class can not be overrode by user command class.

- **Command class routing rules**:

> The relationship between command and it's class is very flexible.
> For example, class of command `test.demo.foo.bar` can be whichever below (with namespace):

``` php
TestDemoFooBar
Test\DemoFooBar
TestDemo\FooBar
Test\Demo\FooBar
TestDemoFoo\Bar
Test\DemoFoo\Bar
TestDemo\Foo\Bar
Test\Demo\Foo\Bar
```

> Besides, command will find it's class in core namespace `Lif\Core\Cmd` first, and then user namespace `Lif\Cmd` if not found before.

> That is to say, to find command `test.demo.foo.bar`, in the worst situation, `8*2=16` possibilities of command-class-finding progress will be processed.

> It is recommended that using first level class (here is `TestDemoFooBar`) to fast locate command class(one time).

- If command parameters have shell-unsafe special characters, quote and escape them manually please.
