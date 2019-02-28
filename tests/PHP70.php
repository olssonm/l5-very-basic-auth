<?php namespace Olssonm\VeryBasicAuth\Tests;

use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;

class PHP70 extends \Olssonm\VeryBasicAuth\Tests\VeryBasicAuthTests {
    /** Setup **/
    public function setUp()
    {
        parent::setUp();
        $this->middleware = new VeryBasicAuth;
    }

    /** Teardown */
	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();
		unlink(__DIR__ . '/../src/config.php');
	}
}
