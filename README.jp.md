# Laravel 5 Very Basic Auth

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total downloads][ico-downloads]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Scrutinizer Score][ico-scrutinizer]][link-scrutinizer]

![very-basic-auth](https://user-images.githubusercontent.com/907114/40575964-331559ce-60ef-11e8-8366-aba700fc5567.png)

**利用可能なドキュメントは以下です:**

🇬🇧 [English](README.md)  
🇯🇵 [日本語](README.jp.md)

Laravel 5 Very Basic AuthはLaravel標準の`auth.basic`とは違い、実際のデータベースの情報を使うことなくBasic認証を追加します。

<img width="400" alt="Screenshot" src="https://user-images.githubusercontent.com/907114/29876493-3907afd8-8d9d-11e7-8068-f461855c493b.png">

例えば、開発中のサイトにユーザーをアクセスさせたい時や、まだデータベースやモデルを用意していない時に使うと便利です。あなたのサイトがデータベースを利用していない場合でも、アクセスを制御することができます。

認証に失敗した場合には、"401 Unauthorized"のレスポンスを返します。

#### 注意点

Basic認証は望まないユーザーからのアクセスを排除することができますが、ブルートフォース攻撃に対しては厳密には安全ではありません。もしこのパッケージをセキュリティのために単独で利用するのであれば、ログインの試行回数を制限するために、少なくともApacheかNginxのrate-limitersを確認するべきです。

## 対応バージョン

Laravel                                | l5-very-basic-auth
:--------------------------------------|:----------
`5.1.*/5.2.*`                          | `1.*`
`5.3.*`                                | `2.*`
`^5.4`                                 | `5.*`

#### Laravel4.xを使っている場合

[こちらのgistを見てください](https://gist.github.com/olssonm/ea5561d7ab20fb5c8ddbdac9b556b32b), 古い`Route::filter`を使って、ほぼ同様の機能を利用できます。

## インストール

Composer経由

``` bash
$ composer require olssonm/l5-very-basic-auth
```

このパッケージのv4.* (for Laravel 5.5)以降では、サービスプロバイダーからパッケージを読み込むのに、パッケージのオートディスカバリーを使用しています。パッケージをインストールすると、以下のメッセージが表示されるはずです。

```
Discovered Package: olssonm/l5-very-basic-auth
```

もしも手動でプロバイダーに追加したい場合は、composer.jsonファイルでオートディスカバリーを切って、

``` json
"extra": {
    "laravel": {
        "dont-discover": [
            "olssonm/l5-very-basic-auth"
        ]
    }
},
```

(`config/app.php`)のprovidersにプロバイダーを追加してください。

``` php
'providers' => [
    Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider::class
]
```

## 設定

`$ php artisan vendor:publish`のコマンドを実行し、`Provider: Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider`を選んで設定ファイルを公開してください。`$ php artisan vendor:publish --provider="Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider"`でも設定ファイルを公開することができます。

`very_basic_auth.php`のファイルがあなたの`app/config`ディレクトリにコピーされます。ここにusernameやpasswordなどの幾つかの設定を置くことができます。

### 注意

**デフォルトのパスワードはありません。** セキュリティのために(誰もが同じパスワードになってしまわないように)、インストール時にランダムなパスワードが設定されます。個別のパスワードを設定するためにもパッケージ設定の公開をして下さい。

#### ビューとメッセージ

`very_basic_auth.php`ファイルでは、メッセージの代わりにカスタマイズしたビューを設定することができます。

``` php
// ユーザーがオプトアウトするか、キャンセルを押した場合に表示されるメッセージ
'error_message'     => 'You have to supply your credentials to access this resource.',

// エラーメッセージの代わりにviewを使いたい場合は"error_view"のコメントアウトを外して下さい。
// この場合、あなたのデフォルトのレスポンスメッセージよりもエラービューが優先されます。
// 'error_view'        => 'very_basic_auth::default'
```

`error_view`のコメントアウトを外した場合、ミドルウェアは指定されたviewを探そうとします。このビュー名は通常と同じように`.blade.php`の拡張子無しで設定してください。

*以前のバージョンから2.1にアップグレードする場合、このkeyとvalueは公開された設定には存在しないので、自分自身で設定を追加する必要があります。*

## 使い方

このミドルウェアはルートを保護するのに`auth.very_basic`の短縮キーを使います。`Route::group()`に適用して複数のルートを保護することもできますし、個別に保護するルートを選ぶこともできます。

**グループを使う場合**
``` php
Route::group(['middleware' => 'auth.very_basic'], function() {
    Route::get('/', ['as' => 'start', 'uses' => 'StartController@index']);
    Route::get('/page', ['as' => 'page', 'uses' => 'StartController@page']);
});
```

**単独で使う場合**
``` php
Route::get('/', [
    'as' => 'start',
    'uses' => 'StartController@index',
    'middleware' => 'auth.very_basic'
]);
```

## テスト

``` bash
$ composer test
```

または

``` bash
$ phpunit
```

テストを実行する際は、Laravelは常にenvironmentの値を"testing"にします。`testing`が`very_basic_auth.php`の`envs`配列内に存在することを確認して下さい。

## ライセンス

MITライセンスです。 詳しくはこちらを見てください。[License File](LICENSE.md)

© 2019 [Marcus Olsson](https://marcusolsson.me).

[ico-version]: https://img.shields.io/packagist/v/olssonm/l5-very-basic-auth.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/olssonm/l5-very-basic-auth/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/olssonm/l5-very-basic-auth.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/olssonm/l5-very-basic-auth.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/olssonm/l5-very-basic-auth
[link-travis]: https://travis-ci.org/olssonm/l5-very-basic-auth
[link-scrutinizer]: https://scrutinizer-ci.com/g/olssonm/l5-very-basic-auth
