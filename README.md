Hooky
=========

[![Packagist](https://img.shields.io/packagist/v/mascame/hooky.svg?maxAge=2592000?style=plastic)](https://packagist.org/packages/mascame/hooky)
[![Travis](https://img.shields.io/travis/marcmascarell/hooky.svg?maxAge=2592000?style=plastic)](https://travis-ci.org/marcmascarell/hooky)
[![license](https://img.shields.io/github/license/marcmascarell/hooky.svg?maxAge=2592000?style=plastic)](https://github.com/marcmascarell/hooky)

Simple hooks.

Installation
--------------

`composer require mascame/hooky`

Usage
--------------

`somewhere.php`
```php

class MyHook implements \Mascame\Hooky\HookContract {
    public function handle($data, $next)
    {
        $data = 'test';

        return $next($data);
    }
}

```

`somewhere.php`
```php

$hook = new \Mascame\Hooky\Hook();
$hookName = 'bar';
$someData = 'foo'; // This will be sent to handlers, where they can manipulate it

$hook->to($hookName, [MyHook::class, AnotherHook::class]);

$data = $hook->fire($hookName, $someData);

```

Contributing
----

Thank you for considering contributing! You can contribute at any time forking the project and making a pull request.

Support
----

If you need help or any kind of support, please send an e-mail to Marc Mascarell at marcmascarell@gmail.com.

License
----

MIT
