<?php namespace Olssonm\VeryBasicAuth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;

class VeryBasicAuthTests extends \Orchestra\Testbench\TestCase {

	public function setUp()
    {
        parent::setUp();
        $this->middleware = new VeryBasicAuth;
    }

    /**
     * Load the package
     * @return array the packages
     */
    protected function getPackageProviders($app)
    {
        return [
            'Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider'
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

		$this->assertEquals('Basic', $result->headers->get('www-authenticate'));
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

		$this->assertEquals('Basic', $result->headers->get('www-authenticate'));
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

		$this->assertEquals('Basic', $result->headers->get('www-authenticate'));
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

		$this->assertEquals('Basic', $result->headers->get('www-authenticate'));
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

	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();
		unlink(__DIR__ . '/../src/config.php');
	}
}
