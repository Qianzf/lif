## Basic usage

``` shell
php lif command [options] [parameters]

# Examples:

php lif user.email.notify --debug --query is_new_user=true
```

## With options

``` shell
php lif -V
php lif help
php lif cmd.list --all
php lif cmd.list --core
php lif cmd.list --user
```

## With parameters

- JSON

``` shell
php lif db.cache.clear --json-file p.json
php lif db.cache.clear --json {"a":"1"}
```

- XML

``` shell
php lif route.cache.clear --xml-file p.xml
php lif route.cache.clear --xml-file <xml><a>1</a></xml>
```

- URL query string

``` shell
php lif view.cache.clear --query-file /path/to/file
php lif view.cache.clear --query 'a=1&b=2'
```

## Notice

- Core command class can not be overrode by user command class.

- **Command class routing rules**:

> The relationship between command and it's class is very flexible, For example, class of command `test.demo.foo.bar` can be whichever below (with namespace):

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

> That is to say, in most bad situation, `8*2=16` possibilities of command class name will be checked to find command `test.demo.foo.bar`.

- If command parameters have shell-unsafe characters, like `&`/`"`/`/`, etc, quote and escape them manually please.
