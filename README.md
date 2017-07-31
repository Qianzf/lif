##### TODO or Doing

- Model/ORM

- Log

- Support variables assignment in route definition

- Add CLI features(jobs, queue, update db, etc...)

- Cache

- Views

##### Current Features

###### Route

- Basic route definition

- Nested route groups

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

LiF use git client hook `pre-commit` to increase version raw counts

- First we create a shell script file:

```
chmod +x .git/hooks/pre-commit > .git/hooks/pre-commit
```

- Then contented with these:

``` shell
#!/bin/sh

ver="`pwd`/.ver"
# Plus 1 here because git client hook `pre-commit` always lag 1 time
cnt="`git rev-list --all --count`"
let cnt++
echo $cnt > $ver
git add -A
echo "\nUpdated LiF version raw counts to $cnt\n"
```

Done!

After that everytime before we commit, version raw counts will increase 1.
