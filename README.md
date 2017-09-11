Another simple, quick, lightweight, API-based PHP Framework.

It's also stands for the recursive acronym ___LiF: LiF is Framework___.

> It's not full-finished yet, but in my active development.

##### TODO

- IoC/DI Container

- Model/ORM

- Add CLI features(jobs, queue, update db, etc...)

- Support variables assignment on route prefix

- Log

- Core configuration to enable or disable some time-wasting but will still be used features

- Version control database

- Cache

- Mock data

- Self-test, self-deploy, shell-based tasks handle

##### Current Features

###### Route

- Basic, quick, dynamic-load route definition

- Nested route groups(without `use ($app)`)

- Support basic variables assignment on route name and auto pass into controller

###### Request

Support Basic functions, auto amount to the main application instance.

###### Middleware

Execute before controllers and after request.

###### Controller

- Support object auto injection in controller/container.

###### Configuration

- Custom configuration

Support any amount of custom configuration files.

- Dynamic configuration

Support modification of php formatted configuration files.

###### Useful Helper functions

Well, I think you will like them, all in _app/core/aux/_.

###### Collection

Support the transformation between array and collection class.

###### Exception

LiF-styled exception output.

###### DB SQL builder

LiF has it's own database SQL query builder, it's simple, and extends functionalities from base PDO class, which handles all the SQL things.

###### Views

LiF is an API-based web framework, so I didn't waste time to design a specialized view template engine. So LiF use plain PHP grammars to write view template. It's has these features:

- Native PHP code, no new syntax to learn

- Reuse with template layouts and sections

- Cache or not can be confinable

- Data share between templates

##### About Version

LiF use git client hook `pre-commit` to increase version raw counts, and use `get_lif_ver()` to calculate the version automatically. (cause i am too lazy to ponder how to version my project)

- First we create a shell script file:

```
chmod +x .git/hooks/pre-commit > .git/hooks/pre-commit
```

- Then contented with:

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
