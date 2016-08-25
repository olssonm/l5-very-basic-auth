# Laravel 5 Very Basic Auth

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]

This package allows you to add a HTTP Basic Auth filter on your routes, without the need to actually use a database – which the Laravel default `auth.basic`-middleware relies on.

![Screenshot](https://cloud.githubusercontent.com/assets/907114/9154094/a34c231a-3e80-11e5-81cc-993b844d6e2f.png)

Perfect if you want to give for example clients access to your development site, and you have yet to set up your database and/or models. Or perhaps your site doesn't even use a database and you still wish to keep it protected.

#### A thing to note

While HTTP Basic Auth does give you a protection layer against unwanted visitors, it is still not strictly safe from brute-force attacks. If you are solely using this package for security, you should at least consider looking into Apache or Nginx rate-limiters to limit login attempts.

## Version Compatibility

 Laravel  | l5-very-basic-auth
:---------|:----------
 5.1.x    | 1.x
 5.2.x    | 1.x
 5.3.x    | 2.x

## Install

Via Composer

``` bash
$ composer require olssonm/l5-very-basic-auth
```

Pop in the provider in the providers array (`config/app.php`).

``` php
'providers' => [
    Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider::class
]
```

Run the command `$ php artisan vendor:publish` to publish the configuration – the file `very_basic_auth.php` will be copied to your `app/config`-folder. Here you can set various options, such as username and password.

#### Note

There is no default password. Upon installation a random password is set for added security. Always use `$ php artisan vendor:publish --provider="Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider"` to be able to set the credentials to access your secured routes.

## Usage

The middleware uses the `auth.very_basic`-filter to protect routes. You can either use `Route::group()` to protect multiple routes, or chose just to protect them individually.

**Group**
``` php
Route::group(['middleware' => 'auth.very_basic'], function() {
    Route::get('/', ['as' => 'start', 'uses' => 'StartController@index']);
    Route::get('/page', ['as' => 'page', 'uses' => 'StartController@page']);
});
```

**Single**
``` php
Route::get('/', [
    'as' => 'start',
    'uses' => 'StartController@index',
    'middleware' => 'auth.very_basic'
]);
```

## Testing

``` bash
$ composer test
```

or

``` bash
$ phpunit
```

Laravel always runs in the "testing" environment while running tests. Make sure that `testing` is set in the `envs`-array in `very_basic_auth.php`.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

© 2015 [Marcus Olsson](https://marcusolsson.me).

[ico-version]: https://img.shields.io/packagist/v/olssonm/l5-very-basic-auth.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/olssonm/l5-very-basic-auth/master.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/olssonm/l5-very-basic-auth
[link-travis]: https://travis-ci.org/olssonm/l5-very-basic-auth
