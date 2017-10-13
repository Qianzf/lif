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

- Core command class can not be overide by user command class.

- If query string has shell-unsafe characters, like `&`/`"`/`/`, quote or escape them  please.
