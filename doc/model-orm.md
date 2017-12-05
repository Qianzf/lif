Basic DI /`find()/save()/delete()` examples:

``` php
use Lif\Mdl\User;

class Test extends Ctl
{
    public function (User $user)
    {
        $user = $user->find(share('user.id'));
    }

    public function create(User $user)
    {
        $user->name = 'cjli';
        $user->role = 'admin';

        return $user->save();    // > 0 => success
    }

    public function update(User $user)
    {
        $user = $user->whereId(share('user.id'))->first();

        $user->name = 'lcj';
        $user->role = 'tester';

        return $user->save();    // > 0 => success
    }

    public function delete(User $user)
    {
        $user = $user->whereId(share('user.id'))->first();

        return $user->delete();    // > 0 => success
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
            'uid',
            null,
            0,
            20, [
                'at' => 'desc'
            ]
        );

        // Or:
        return $this->hasMany([
            'model' => Trending::class,
            'lk'    => 'id',
            'fk'    => 'uid',
            // 'lv'   => null,
            // 'from' => 0,
            // 'take' => 20,
            'sort' => [
                'at' => 'desc',
            ],

            // Dynamic where conditions in master join table
            'lwhere' => [
                'type' => 'story'
            ],

            // Dynamic where conditions in sub join table
            'fwhere' => [
                'task' => [
                    'cnd' => '>',
                    'val' => 1,
                ],
            ],
        ]);
    }
}
class Trending extends Mdl
{
    public function user(...$args)
    {
        return $this->belongsTo(
            User::class,
            'uid',
            'id',
            null
        );

        // Or:
        return $this->belongsTo([
            'model' => User::class,
            'lk'    => 'uid',
            'fk'    => 'id',
            // 'lv'    => null,
        ]);
    }
}
```
