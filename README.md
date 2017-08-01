##### TODO

- Model/ORM

- Support variables assignment on route prefix

- Core configuration to enable or disable some time-wasting but will be used features

- Log

- Version control database

- Add CLI features(jobs, queue, update db, etc...)

- Cache

- Self-test

- Mock data

- Self-deploy

- Views

##### Current Features

###### Route

- Basic route definition

- Nested route groups

- Support basic variables assignment on route name and auto pass into controller

###### Request

Basic functions supported.

###### Middleware

Execute before controllers and after request.

###### Controller

###### Configuration

- Custom configuration

- Dynamic configuration

###### Useful Helper functions

###### Exception & Error handle

##### About Version

LiF use git client hook `pre-commit` to increase version raw counts, and use `get_lif_ver()` to calculate the version automatically. (cause i am too lazy to ponder how to version my project)

- First we create a shell script file:

```
chmod +x .git/hooks/pre-commit > .git/hooks/pre-commit
```

- Then contented with these:

``` shell
#!/bin/sh

ver="`pwd`/.ver"
cnt="`git rev-list --all --count`"
# Plus 1 here because git client hook `pre-commit` always lag 1 time
let cnt++
echo $cnt > $ver
git add -A
echo "\nUpdated LiF version raw counts to $cnt\n"
```

Done!

After that everytime before we commit, version raw counts will increase 1.
