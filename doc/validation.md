#### Defination

- Grammer: `mixed validate(array $data, array $rules)`.

- Parameters:

`$data`: assoc array. The data to be validated.

`$rules`: assoc array. The rules to validate `$data`.

- Return values:

Boolean `true` if validation success, language key if validation fails.

language key can be used with `lang()` to output error message.

#### Examples

- In controller

``` php
namespace Lif\Ctl\Ldtdf\Admin;

use Lif\Mdl\User as UserModel;

class User extends Ctl
{
    public function add(UserModel $user)
    {
        // Will auto redirect to web page with errors when validation fails
        $this->validate($this->request->all(), [
            'account' => 'need',
            'name'    => 'need',
            'passwd'  => 'need',
            'role'    => 'need|in:ADMIN,TESTER,DEVELOPER',
        ]);

        // do sth more ...
    }
}
```

- Solely

``` php
// Use global `validate()` to validate array data anywhere
$data = [
    'email1' => 'test'
    'email2' => 'test@cjli.info'
];

$rules = [
    'email1' => 'email',
    'email2' => 'need|email',
];

$prepare = validate($data, $rules);    // fails, return language key

if (true !== $prepare && ('web' == context())) {
    share('__error', sysmsg($prepare));
    redirect('/'.$this->route);
}

dd($prepare);

// With default and condition
$config = [
    'host' => '127.0.0.1',
    'ras'  => '/path/to/rsa',
];
if (true !== ($err = validate($config, [
    'host' => 'need|host',

    // if valition `int|min:1` fails
    // `$config['port']` will be set to 22
    // (except `need` validator is given)
    'port' => ['int|min:1', 22],

    // if valition `in:pswd,ssh` fails
    // `$config['auth']` will be set to 'ssh'
    // (except `need` validator is given)
    'auth' => [
        'rule'    => 'in:pswd,ssh',
        'default' => 'ssh'
    ],

    // when `$config['auth']` exists and equals to 'pswd'
    // then `$config['user']` is necessary and will be validated by given rules
    // else skip the validation for `user` field
    'user' => 'when:auth=pswd|string',
    'pswd' => 'when:auth=pswd|string',
    'rsa'  => 'when:auth=ssh|string'
]))) {
    excp('Illegal server configs: '.$err);
}
```

- `legal_or()`

`legal_or()` can also be used to validate an given array data with given rules.

The mainly difference between `validate()` and `legal_or()` is that `legal_or()` can assign a default value when any value of that array data validation fails.

For example:

``` php
$errs = legal_or($request, [
    'search' => ['string', ''],
    'role'   => ['in:ADMIN,DEVELOPER,TESTER', false],
    'page'   => ['int|min:1', 1],
]);

dd($errs);
```

In this case, if `$request['page']` is not an integer over 1, then `$request['page']` will be transformed into exactly integer `1`.

It's useful in some scenarios like dynamic search query strings.

Besides, `$errs` stores the validation results, it's an array with same keys of validating data (here's `$request`), and it's values are validation results for each item, which are compatible with `validate()`.

For example, in this case, return value of `legal_or` will be like this:

``` php
// $errs

Array
(
    search => true,
    role   => true,
    page   => ILLEGAL_PAGE,
)
```

`$errs` is useful when you need to give some specific notice infos to client.

- `legal_and`

Basically, `legal_and()` do the same things that `validate()` can do, but one more thing than `validate()` is, `legal_and()` auto assigning validated legal value into given vars.

For examples:

``` php
$name   = $table = null;
$config = [
    'name'  => 'sqlite'
    'table' => 'lif',
];

if (true !== ($err = legal_and($config, [
    'name'  => 'need|string',
    'table' => ['need|string', &$table],
]))) {
    excp('Illegal config: '.$err);
}

dd($name, $table);    // Output: null, lif
```

In this case, `$config` is validate, so the value of `$table` turns into string `lif` after validation, but `$name` was not give into rules array, so `$name` still is `null`.

**Well, of course, `&` is necessary before the value to be auto asisigned. Or they will not be asisigned with validated values.**
