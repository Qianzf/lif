A simple, quick, lightweight, API-based modern PHP Framework.

> It's also stands for the recursive acronym ___LiF: LiF is Framework___.

## Features

### Route

- Basic, quick, dynamic-load route definition

- Nested route groups(without `use ($app)`)

- Support variables assignment on both route prefix and route name, and auto pass into controller

- Support route parameters filtering

- Support routes cache (Colsure not supported)

### Request

Support Basic functions, auto amount to the main application instance.

### Middleware

Execute before controllers and after request.

### Controller

Support object auto injection in controller/container.

### Useful Helper functions

Well, I think you will like them, all in _app/core/aux/_.

### DB SQL builder

LiF has it's own database SQL query builder, it's simple, and extends functionalities from base PDO class, which handles all the SQL things.

### Model/ORM

Support basic Model/ORM functionalities.

### Views

LiF is an API-based web framework, so I didn't waste time to design a specialized view template engine. So LiF use plain PHP grammars to write view template. It's has these features:

- Native PHP code, no new syntax to learn

- Reuse with template layouts and sections

- Cache or not can be confinable

- Data share between templates

### CLI

- Support basic command line features

- Support user-customized command class

### Configuration

- Custom configuration

Support any amount of custom configuration files.

- Dynamic configuration

Support modification of php formatted configuration files.

### Collection

Support the transformation between array and collection class.

### Validation

Support custom validation and keep an uniform return values: false or language key.

### I18N

Use `sysmsg()`/`lang()` and dynamic language packages, LiF can easily achieve i18n.

### Exception

LiF-styled exception output.

### Libraries

LiF re-packaged with some common used component's APIs in a more easier way to use .

#### Mail

Use `email()` to send an email is very simple, see more details in doc.

## Documentations

See: _var/doc_.

> TODO (It's not full-finished yet, but in my active development)

> - Log

> - Enable or disable some time-wasting but will still be used features via Core configurations

> - Queue

> - Version control database

> - Mock data

> - Self-test, self-deploy, shell-based tasks handle
> 

## LICENSE

LiF is open-sourced under [MIT license](https://opensource.org/licenses/MIT).
