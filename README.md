A simple, quick, lightweight, API-based modern PHP Framework.

> It's also stands for the recursive acronym ___LiF: LiF is Framework___.

## Features

### Route

- Basic, quick, dynamic-load route definition

- Nested route groups(without `use ($app)`)

- Support variables assignment on both route prefix and route name, and auto pass into controller

- Support route parameters filtering

- Support routes cache (Closure not supported)

### Request

Support Basic functions, auto amount to the main application instance.

### Middleware

- Passing: Execute before controller and after request.

- Callback: Execute before PHP exit and after controller.

### Controller

Support object auto injection in controller/container.

### Useful Helper functions

Well, I think you will like them, all in _app/core/aux/_.

### Storage Layer

- Query builder

LiF has it's own database SQL query builder, it's simple but handles all the SQL query things.

- Schema builder

LiF has it's own schema creator, which handles the SQL database definition and schema management.

- Dit

Version control database implementation in LiF.

> It's naming borrows from Git.

### Model/ORM

Support basic and flexible Model/ORM functionalities.

### Views

LiF is an API-based web framework, I didn't waste time to design a specialized view template engine. So LiF use plain PHP grammars to write view template, which has these features:

- Native PHP code, no new syntax to learn

- Code reuse via template layouts and sections

- Cache or not can be configurable

- Data share between templates

### CLI

- Support basic command line features

- Support user-customized command class

- Suppert unified outer commands executing locally and remotely(via SSH)

### Configuration

- Custom configuration

Support any amount of custom configuration files.

- Dynamic configuration

Support modification of php formatted configuration files.

### Collection

Support the transformation between array and collection class.

### Validation

Support custom validation and keep an uniform return values: `false` or language key.

### Log

Support common used loggers follows PSR-3, and in a more flexible way.

### Queue

Support basic queue and queue jobs functionalities.

### Facade

LiF implements a simple way to proxy classes by facade. 

### I18N

Use `sysmsg()`/`lang()` and dynamic language packages, LiF can easily achieve simple i18n.

### Exception

LiF-styled exception output.

### Libraries

LiF re-packaged with some common used component's APIs in a more easier way to use.

#### Mail

Use `email()` to send an email is very simple, see more details in doc.

## Documentations

See: _doc/*_.

## TODO

>It's not full-finished yet, but in my active development

- Unify response and make middleware callback work

- Enable or disable some time-wasting but will still be used features via core configurations

- Mock data

- Self-test, self-deploy, shell-based tasks handle

- GraphQL support

## LICENSE

LiF is open-sourced under [MIT license](https://opensource.org/licenses/MIT).
