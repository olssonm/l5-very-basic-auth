<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;

use function Pest\Laravel\get;

beforeEach(function() {
    Route::get('/', fn () => 'ok')->middleware(VeryBasicAuth::class)->name('default');
    Route::get('/inline', fn () => 'ok')->middleware(
        sprintf('auth.very_basic:%s,%s', config('very_basic_auth.user'), config('very_basic_auth.password'))
    )->name('inline');
});

test('basic auth filter is set', function () {
    $this->assertTrue(in_array(VeryBasicAuth::class, $this->app->router->getMiddleware()));
    $this->assertTrue(array_key_exists('auth.very_basic', $this->app->router->getMiddleware()));
});

test('config file is installed', function() {
    $this->assertTrue(file_exists(__DIR__ . '/../src/config.php'));
});

test('request with no credentials fails', function() {
    $response = get('/');

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')), $response->headers->get('WWW-Authenticate'));
    $this->assertEquals(config('very_basic_auth.error_message'), $response->getContent());
});

test('request with incorrect credentials fails - text/html', function () {
    $response = $this->withHeaders([
        'PHP_AUTH_USER' => str_random(20),
		'PHP_AUTH_PW' => str_random(20)
    ])->get('/');

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('content-type'));
    $this->assertEquals(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')), $response->headers->get('WWW-Authenticate'));
    $this->assertEquals(config('very_basic_auth.error_message'), $response->getContent());
});

test('request with incorrect credentials fails - json', function () {
    $response = $this->withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20),
        'Accept' => 'application/json'
    ])->get('/');

    $content = json_decode($response->getContent());

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('application/json', $response->headers->get('content-type'));
    $this->assertEquals(json_last_error(), JSON_ERROR_NONE);
    $this->assertEquals(config('very_basic_auth.error_message'), $content->message);
    $this->assertEquals(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')), $response->headers->get('WWW-Authenticate'));
});

test('request with incorrect credentials fails - view', function () {

    config()->set('very_basic_auth.error_view', 'very_basic_auth::default');

    $response = $this->withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20)
    ])->get('/');

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('content-type'));
    $this->assertEquals(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')), $response->headers->get('WWW-Authenticate'));
    $this->assertStringContainsStringIgnoringCase('This is the default view for the olssonm/l5-very-basic-auth-package', $response->getContent());
});

test('request with correct credentials passes', function () {
    $response = $this->withHeaders([
        'PHP_AUTH_USER' => config('very_basic_auth.user'),
        'PHP_AUTH_PW' => config('very_basic_auth.password')
    ])->get('/');

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('ok', $response->getContent());
});

test('environments', function () {
    config()->set('very_basic_auth.envs', ['production']);
    $this->get('/')->assertStatus(200);

    config()->set('very_basic_auth.envs', ['local']);
    $this->get('/')->assertStatus(200);

    config()->set('very_basic_auth.envs', ['*']);
    $this->get('/')->assertStatus(401);

    config()->set('very_basic_auth.envs', ['testing']);
    $this->get('/')->assertStatus(401);
});

test('request with incorrect inline credentials fails', function () {
    $response = $this->withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20)
    ])->get('/inline');

    $this->assertEquals(401, $response->getStatusCode());
    $this->assertEquals(config('very_basic_auth.error_message'), $response->getContent());
});

test('request with correct inline credentials passes', function () {
    $response = $this->withHeaders([
        'PHP_AUTH_USER' => config('very_basic_auth.user'),
        'PHP_AUTH_PW' => config('very_basic_auth.password')
    ])->get('/inline');

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('ok', $response->getContent());
});
