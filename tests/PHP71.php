<?php namespace Olssonm\VeryBasicAuth\Tests;

use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;

class PHP71 extends \Olssonm\VeryBasicAuth\Tests\VeryBasicAuthTests {
    /** Setup **/
    public function setUp(): void
    {
        parent::setUp();
        $this->middleware = new VeryBasicAuth;
    }

    /** Teardown */
	public static function tearDownAfterClass(): void
	{
		parent::tearDownAfterClass();
		unlink(__DIR__ . '/../src/config.php');
	}
}
