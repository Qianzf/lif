Controller examples:

#### Dependency Injection

- Route

``` php
$this->get('user/{id}', 'User@query');
```

- Controller

``` php
namespace Lif\Ctl;

use Lif\Mdl\User as UserModel;

class User extends Ctl
{
    public function query(UserModel $user)
    {
        response([
            'id' => $user->id
        ]);
    }
}
```
