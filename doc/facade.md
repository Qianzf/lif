#### Create a facade

``` php
namespace Lif\Facade;

use Lif\Core\Abst\Facade;

class Validation extends Facade
{
    // Set proxy by static property
    protected static $proxy = '';

    // Or by static method
    public static function getProxy()
    {
        return nsOf('core', 'Validation');
    }
}
```

#### Use a facade

``` php
use Lif\Facade\Validation;

dd(Validation::run([
    'id' => 'abcd',
], [
    'id' => 'need|int|min:1'
]));

dd(Validation::email('abc'));    // ILLEGAL_EMAIL
dd(Validation::email('a@b.c'));  // `true`
```
