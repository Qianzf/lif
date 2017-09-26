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
```
- `legal_or()`

Use `legal_or()` can also be used to validate an given array data with given array rules.

The mainly different between `validate()` and `legal_or()` is that `legal_or()` can assign a default value when any value of that array data validation fails.

For example:

``` php
legal_or($request, [
    'search' => ['string', ''],
    'role'   => ['in:ADMIN,DEVELOPER,TESTER', false],
    'page'   => ['int|min:1', 1],
]);
```

In this case, if `$request['page']` is not an integer over 1, then `$request['page']` will be transfered into exactly integer `1`.

It's useful in some scenarios like dynamic search query strings.
