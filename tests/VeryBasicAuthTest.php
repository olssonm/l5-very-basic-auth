<?php

use Olssonm\VeryBasicAuth\Handlers\DefaultResponseHandler;
use Olssonm\VeryBasicAuth\Handlers\ResponseHandler;
use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;
use Olssonm\VeryBasicAuth\Tests\Fixtures\CustomResponseHandler;

use function Pest\Laravel\get;
use function Pest\Laravel\withHeaders;

test('basic auth filter is set', function () {
    expect(in_array(VeryBasicAuth::class, $this->app->router->getMiddleware()))->toBeTrue();
    expect(array_key_exists('auth.very_basic', $this->app->router->getMiddleware()));
});

test('config file is installed', function () {
    expect(file_exists(__DIR__.'/../src/config.php'))->toBeTrue();
});

test('install package', function () {
    $this->artisan('vendor:publish', [
        '--provider' => 'Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider',
    ])->assertExitCode(0);

    expect(file_exists(config_path('very_basic_auth.php')))->toBeTrue();
});

test('request with no credentials and no config passes', function () {
    config()->set('very_basic_auth.user', '');
    config()->set('very_basic_auth.password', '');

    $response = get('/');

    expect($response->getStatusCode())->toEqual(200);
    expect($response->headers->get('WWW-Authenticate'))->toEqual(null);
});

test('request with no credentials fails', function () {
    $response = get('/');

    expect($response->getStatusCode())->toEqual(401);
    expect($response->headers->get('WWW-Authenticate'))->toEqual(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')));
    expect($response->getContent())->toEqual(config('very_basic_auth.error_message'));
});

test('request with incorrect credentials fails - text/html', function () {
    $response = withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20),
    ])->get('/');

    expect($response->getStatusCode())->toEqual(401);
    expect($response->headers->get('content-type'))->toEqual('text/html; charset=UTF-8');
    expect($response->headers->get('WWW-Authenticate'))->toEqual(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')));
    expect($response->getContent())->toEqual(config('very_basic_auth.error_message'));
});

test('request with incorrect credentials fails - hashed password', function () {

    config()->set('very_basic_auth.user', 'test');
    config()->set('very_basic_auth.password', app()->make('hash')->make('test'));

    $response = withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20),
    ])->get('/');

    expect($response->getStatusCode())->toEqual(401);
});

test('request with incorrect credentials fails - json', function () {
    $response = withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20),
        'Accept' => 'application/json',
    ])->get('/');

    $content = json_decode($response->getContent());

    expect($response->getStatusCode())->toEqual(401);
    expect($response->headers->get('content-type'))->toEqual('application/json');
    expect(json_last_error())->toEqual(JSON_ERROR_NONE);
    expect($content->message)->toEqual(config('very_basic_auth.error_message'));
    expect($response->headers->get('WWW-Authenticate'))->toEqual(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')));
});

test('request with incorrect credentials fails - view', function () {
    config()->set('very_basic_auth.error_view', 'very_basic_auth::default');

    $response = withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20),
    ])->get('/');

    expect($response->getStatusCode())->toEqual(401);
    expect($response->headers->get('content-type'))->toEqual('text/html; charset=UTF-8');
    expect($response->headers->get('WWW-Authenticate'))->toEqual(sprintf('Basic realm="%s", charset="UTF-8"', config('very_basic_auth.realm')));

    $this->assertStringContainsStringIgnoringCase('This is the default view for the olssonm/l5-very-basic-auth-package', $response->getContent());
});

test('request with correct credentials passes', function () {
    $response = withHeaders([
        'PHP_AUTH_USER' => config('very_basic_auth.user'),
        'PHP_AUTH_PW' => config('very_basic_auth.password'),
    ])->get('/');

    expect($response->getStatusCode())->toEqual(200);
    expect($response->getContent())->toEqual('ok');
});

test('request with correct credentials passes - hashed password', function () {
    config()->set('very_basic_auth.user', 'test');
    config()->set('very_basic_auth.password', app()->make('hash')->make('test'));

    $response = withHeaders([
        'PHP_AUTH_USER' => 'test',
        'PHP_AUTH_PW' => 'test',
    ])->get('/');

    expect($response->getStatusCode())->toEqual(200);
    expect($response->getContent())->toEqual('ok');
});

test('environments', function () {
    config()->set('very_basic_auth.envs', ['production']);
    get('/')->assertStatus(200);

    config()->set('very_basic_auth.envs', ['local']);
    get('/')->assertStatus(200);

    config()->set('very_basic_auth.envs', ['*']);
    get('/')->assertStatus(401);

    config()->set('very_basic_auth.envs', ['testing']);
    get('/')->assertStatus(401);
});

test('request with incorrect inline credentials fails', function () {
    $response = withHeaders([
        'PHP_AUTH_USER' => str_random(20),
        'PHP_AUTH_PW' => str_random(20),
    ])->get('/inline');

    expect($response->getStatusCode())->toEqual(401);
    expect($response->getContent())->toEqual(config('very_basic_auth.error_message'));
});

test('request with correct inline credentials passes', function () {
    $response = withHeaders([
        'PHP_AUTH_USER' => config('very_basic_auth.user'),
        'PHP_AUTH_PW' => config('very_basic_auth.password'),
    ])->get('/inline');

    expect($response->getStatusCode())->toEqual(200);
    expect($response->getContent())->toEqual('ok');
});

test('test response handlers', function () {
    // Custom response handler
    app()->bind(
        ResponseHandler::class,
        CustomResponseHandler::class
    );

    $response = get('/test');

    expect($response->getStatusCode())->toEqual(401);
    expect($response->getContent())->toEqual('Custom response');

    // Default response handler
    app()->bind(
        ResponseHandler::class,
        DefaultResponseHandler::class
    );

    $response = get('/test');

    expect($response->getStatusCode())->toEqual(401);
    expect($response->getContent())->toEqual(config('very_basic_auth.error_message'));
});

// Test for the console command PasswordGenerateCommand
test('console command sets password in .env file', function () {
    $envPath = base_path('.env');

    // Clean up any existing .env before the test
    if (file_exists($envPath)) {
        unlink($envPath);
    }

    // Create a fresh .env
    file_put_contents($envPath, "APP_NAME=Laravel\nBASIC_AUTH_PASSWORD=test");

    $password = 'password' . uniqid();

    // Simulate user input for the console command
    $this->artisan('very-basic-auth:password-generate')
        ->expectsQuestion('Please enter a password for the very basic auth', $password)
        ->expectsQuestion('Please confirm your password', $password)
        ->assertExitCode(0);

    // Reload env-variables to make sure the newest value is available
    $this->artisan('config:cache');
    $hashedPassword = config('very_basic_auth.password');

    expect($hashedPassword)->not->toBeNull();
    expect(app()->make('hash')->check($password, $hashedPassword))->toBeTrue();
    expect(config('app.name'))->toEqual('Laravel');
});
