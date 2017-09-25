For example:

``` php
use Lif\Mdl\User;

class Test
{
    public function create(User $user)
    {
        $user->name = 'cjli';
        $user->role = 'ADMIN';

        return $user->save();    // > 0 success
    }

    public function update(User $user)
    {
        $user = $user->whereId(share('__USER.id'))->first();

        $user->name = 'cjli2';
        $user->role = 'TESTER';

        return $user->save();    // > 0 success
    }

    public function delete(User $user)
    {
        $user = $user->whereId(share('__USER.id'))->first();

        return $user->delete();    // > 0 success
    }
}
```
