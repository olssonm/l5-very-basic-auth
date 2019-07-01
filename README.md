# Laravel 5 Very Basic Auth

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total downloads][ico-downloads]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Scrutinizer Score][ico-scrutinizer]][link-scrutinizer]

![very-basic-auth](https://user-images.githubusercontent.com/907114/40575964-331559ce-60ef-11e8-8366-aba700fc5567.png)

**Documentation available in:**

🇬🇧 [English](README.md)  
🇯🇵 [日本語](README.jp.md)

This package allows you to add a HTTP Basic Auth filter on your routes, without the need to actually use a database – which the Laravel default `auth.basic`-middleware relies on.

<img width="400" alt="Screenshot" src="https://user-images.githubusercontent.com/907114/29876493-3907afd8-8d9d-11e7-8068-f461855c493b.png">

Perfect if you want to give for example clients access to your development site, and you have yet to set up your database and/or models. Or perhaps your site doesn't even use a database and you still wish to keep it protected.

On failed authentication the user will get a "401 Unauthorized" response.

#### A thing to note

While HTTP Basic Auth does give you a protection layer against unwanted visitors, it is still not strictly safe from brute-force attacks. If you are solely using this package for security, you should at least consider looking into Apache or Nginx rate-limiters to limit login attempts.

## Version Compatibility

Laravel                                | l5-very-basic-auth
:--------------------------------------|:----------
`5.1.*/5.2.*`                          | `1.*`
`5.3.*`                                | `2.*`
`^5.4`                                 | `5.*`

*The odd versioning is due to breaking changes in the testing framework and PHP versions. Else, `3.x` is usable for Laravel 5.4 (PHP 5.6 and up) and `4.x` for Laravel 5.5.*

#### Using Laravel 4.x?

[Take a look at this gist](https://gist.github.com/olssonm/ea5561d7ab20fb5c8ddbdac9b556b32b), it uses the old `Route::filter`-methods to achieve pretty much the same goal.

## Installation

Via Composer

``` bash
$ composer require olssonm/l5-very-basic-auth
```

Since v4.* (for Laravel 5.5) this package uses Package Auto-Discovery for loading the service provider. Once installed you should see the message

```
Discovered Package: olssonm/l5-very-basic-auth
```

If you would like to manually add the provider, turn of Auto-Discovery for the package in your composer.json-file:

``` json
"extra": {
    "laravel": {
        "dont-discover": [
            "olssonm/l5-very-basic-auth"
        ]
    }
},
```

And then add the provider in the providers array (`config/app.php`).

``` php
'providers' => [
    Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider::class
]
```

## Configuration

Run the command `$ php artisan vendor:publish` and select `Provider: Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider` to publish the configuration. You could also type `$ php artisan vendor:publish --provider="Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider"` to directly publish the files.

The file `very_basic_auth.php` will then be copied to your `app/config`-folder – here you can set various options such as username and password.

#### Note

**There is no default password**. Upon installation a random password is set for added security (we don't want everyone to use the same default password). Please publish the packages configuration to have the ability to set a custom password.

### Environments

You may set the environments that the package should be applied for. You may simply use "`*`" to use in all environments (this is also the default).

``` php
'envs' => [
    '*'
],
```

Or

``` php
'envs' => [
    'production',
    'development',
    'local'
],
```

### Views and messages

In the `very_basic_auth.php`-configuration you have the ability to set a custom view instead of a message.

``` php
// Message to display if the user "opts out"/clicks "cancel"
'error_message'     => 'You have to supply your credentials to access this resource.',

// If you prefer to use a view with your error message you can uncomment "error_view".
// This will supersede your default response message
// 'error_view'        => 'very_basic_auth::default'
```

If you uncomment `error_view`, the middleware will try to find your specified view. You supply this value as usual (without the `.blade.php`-extention).

*If you've upgraded to 2.1 from a previous version this key and value will be missing from your published configuration and you will have to add it yourself.*

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

You may also set the credentials inline;

``` php
Route::get('/', [
    'as' => 'start',
    'uses' => 'StartController@index',
    'middleware' => 'auth.very_basic:username,password'
]);
```

*Note:* inline credentials always take president over the `very_basic_auth.php`-configuration file.

## Testing

``` bash
$ composer test
```

or

``` bash
$ phpunit
```

Laravel always runs in the "testing" environment while running tests. Make sure that `testing` is set in the `envs`-array in `very_basic_auth.php`.

## Thank you

A big thank you to the people who has contributed to this package, among others:

**[kazuhei](https://github.com/kazuhei)** – for providing the awesome Japanese translation  
**[freekmurze](https://github.com/freekmurze)** – for additional information on package/vendor installations  
**[faiare](https://github.com/faiare)** – for pointing out and implementing the `realm`-attribute ([RFC7235](https://tools.ietf.org/html/rfc7235#section-2.2))


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

© 2019 [Marcus Olsson](https://marcusolsson.me).

[ico-version]: https://img.shields.io/packagist/v/olssonm/l5-very-basic-auth.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/olssonm/l5-very-basic-auth/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/olssonm/l5-very-basic-auth.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/olssonm/l5-very-basic-auth.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/olssonm/l5-very-basic-auth
[link-travis]: https://travis-ci.org/olssonm/l5-very-basic-auth
[link-scrutinizer]: https://scrutinizer-ci.com/g/olssonm/l5-very-basic-auth
