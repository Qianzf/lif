###### TODO

- model

###### Version

- use git client hook `pre-commit`

``` shell
#!/bin/sh

ver="`pwd`/.ver"
git rev-list --all --count > $ver
git add -A
echo "Updated LiF version raw counts to `cat $ver`"
```
