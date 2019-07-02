<?php namespace Olssonm\VeryBasicAuth\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;

class VeryBasicAuthTests extends \Orchestra\Testbench\TestCase {

	protected $middleware;

    /**
     * Load the package
     * @return array the packages
     */
    protected function getPackageProviders($app)
    {
        return [
            \Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider::class
        ];
    }

	/** @test */
	public function test_very_basic_auth_route_filter_is_set()
	{
		$middlewares = $this->app->router->getMiddleware();
		$this->assertTrue(in_array('Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth', $middlewares));
		$this->assertTrue(array_key_exists('auth.very_basic', $middlewares));
	}

	/** @test */
	public function test_config_file_is_installed()
	{
		// Look for config.php
		$this->assertTrue(file_exists(__DIR__ . '/../src/config.php'));
	}

	/** @test */
	public function test_very_basic_auth_authenticate_no_credentials()
	{
		$request = new Request();
        $response = new JsonResponse();

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

		$this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());
        $this->assertEquals(config('very_basic_auth.error_message'), $result->getContent());
	}

	/** @test */
	public function test_very_basic_auth_authenticate_incorrect_credentials()
	{
		$request = new Request();
        $response = new JsonResponse();

		$user = str_random(20);
		$pass = str_random(20);

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

        $this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());
        $this->assertEquals(config('very_basic_auth.error_message'), $result->getContent());
	}

	/** @test */
	public function test_very_basic_auth_authenticate_incorrect_password()
	{
		$request = new Request();
        $response = new JsonResponse();

		$user = config('very_basic_auth.user');
		$pass = str_random(20);

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

        $this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());
        $this->assertEquals(config('very_basic_auth.error_message'), $result->getContent());
	}

	/** @test */
	public function test_very_basic_auth_authenticate_incorrect_user()
	{
		$request = new Request();
        $response = new JsonResponse();

		$user = str_random(20);
		$pass = config('very_basic_auth.password');

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

        $this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());
        $this->assertEquals(config('very_basic_auth.error_message'), $result->getContent());
	}

	/** @test */
	public function test_very_basic_auth_authenticate_correct_credentials()
	{
		$request = new Request();
        $response = new JsonResponse();

		$user = config('very_basic_auth.user');
		$pass = config('very_basic_auth.password');

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

		$this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{}', $result->getContent());
	}

	/** @test */
	public function test_very_basic_auth_json_failed_response()
	{
		$request = new Request();
        $response = new JsonResponse();

		$user = config('very_basic_auth.user');
		$pass = str_random(20);

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);
		$request->headers->add(['Accept' => 'application/json']);

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

		$content = json_decode($result->getContent());

	   	$this->assertEquals(401, $result->getStatusCode());
		$this->assertEquals(json_last_error(), JSON_ERROR_NONE);
		$this->assertEquals('application/json', $result->headers->get('content-type'));
	    $this->assertEquals(config('very_basic_auth.error_message'), $content->message);
	}

	/** @test */
	public function test_very_basic_auth_view_incorrect_credentials()
	{
		// Set the middleware to use a view
		config()->set('very_basic_auth.error_view', 'very_basic_auth::default');

		$request = new Request();
        $response = new JsonResponse();

		$user = str_random(20);
		$pass = str_random(20);

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

        $this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());

		// PHPUNIT 7.5.6+
		if (method_exists($this, 'assertStringContainsStringIgnoringCase')) {
			$this->assertStringContainsStringIgnoringCase('This is the default view for the l5-very-basic-auth-package', $result->getContent());
		} else {
			$this->assertContains('This is the default view for the l5-very-basic-auth-package', $result->getContent());
		}
	}

	/* test */
	public function test_very_basic_auth_env_local()
	{
		// Set the environment to only be "local"
		config()->set('very_basic_auth.envs', ['local']);

		$request = new Request();
		$response = new JsonResponse();
		$next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

		// 200 becouse we should be locked out; tests occurs in the testing env.
		$this->assertEquals(200, $result->getStatusCode());
	}

	/* test */
	public function test_very_basic_auth_env_testing()
	{
		// Set the environment to only be "testing"
		config()->set('very_basic_auth.envs', ['testing']);

		$request = new Request();
		$response = new JsonResponse();
		$next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

        $this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());
        $this->assertEquals(config('very_basic_auth.error_message'), $result->getContent());
	}

	/* test */
	public function test_very_basic_auth_env_wildcard()
	{
		// Set the environment to use wildcard
		config()->set('very_basic_auth.envs', ['*']);

		$request = new Request();
		$response = new JsonResponse();
		$next = function($request) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $realm = config('very_basic_auth.realm', 'Basic Auth');

        $this->assertEquals('Basic realm="' . $realm . '", charset="UTF-8"', $result->headers->get('WWW-Authenticate'));
		$this->assertEquals(401, $result->getStatusCode());
        $this->assertEquals(config('very_basic_auth.error_message'), $result->getContent());
	}

	/* test */
	public function test_inline_credentials_success()
	{
		config()->set('very_basic_auth.user', 'test');
		config()->set('very_basic_auth.password', 'test');

		// Use random user and password
		$user = str_random(20);
		$pass = str_random(20);

		$request = new Request();
		$response = new JsonResponse();
		$next = function($request) use ($response) {
            return $response;
        };

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $result = $this->middleware->handle($request, $next, $user, $pass);

		$this->assertEquals(200, $result->getStatusCode());
	}

	/* test */
	public function test_inline_credentials_fail()
	{
		config()->set('very_basic_auth.user', 'test');
		config()->set('very_basic_auth.password', 'test');

		// Use random user and password
		$user = str_random(20);
		$pass = str_random(20);

		$request = new Request();
		$response = new JsonResponse();
		$next = function($request) use ($response) {
            return $response;
        };

		$request->headers->add(['PHP_AUTH_USER' => $user]);
		$request->headers->add(['PHP_AUTH_PW' => $pass]);

        $result = $this->middleware->handle($request, $next, 'test', 'test');

		$this->assertEquals(401, $result->getStatusCode());
	}
}
