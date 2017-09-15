Controller examples:

#### Dependency Injection

- Route

``` php
$this->get('user/{id}', 'User@query');
```

- Controller

``` php
namespace Lif\Ctl;

class User extends Ctl
{
    public function query(\Lif\Mdl\User $user)
    {
        response([
            'id' => $user->id
        ]);
    }
}
```
