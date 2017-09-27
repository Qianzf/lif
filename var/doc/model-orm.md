Basic DI/`find()/save()/delete()` examples:

``` php
use Lif\Mdl\User;

class Test extends Ctl
{
    public function (User $user)
    {
        $user = $user->find(share('__USER.id'));
    }

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

Slightly complex examples, `hasMany()/belongsTo()`.

- Controller:

``` php

use Lif\Mdl\{User as User, Trending};

class Test extends Ctl
{
    // User has many trendings
    public function trending(User $user)
    {    
        view('user/trending')
        ->withTrending(
            $user->trendings()
        );
    }
    // Trending belongs to one user
    public function user(Trending $trending)
    {
        $user = $trending->user();
        // dd($user);
    }
}
```

- Model

``` php
class User extends Mdl
{
    public function trendings()
    {
        return $this->hasMany(
            Trending::class,
            'id',
            'uid'
        );
    }   
}
class Trending extends Mdl
{
    public function user(...$args)
    {
        return $this->belongsTo(
            User::class,
            'uid',
            'id'
        );
    }
}
```
