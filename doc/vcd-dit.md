#### Version controll database

- dit add

``` shell
php lif dit.add -N CreateUserTable
php lif dit.add -N drop_name_of_user_table
```

- dit commit

``` shell
php lif dit.commit

```

- dit status

``` shell
php lif dit.status
```

- dit revert

``` shell
# Revert to last version
php lif dit.revert

# Revert to given version
php lif dit.revert --ver 10
```
