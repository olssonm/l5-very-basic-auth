<?php

namespace Olssonm\VeryBasicAuth\Tests;

use Olssonm\VeryBasicAuth\VeryBasicAuthServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            VeryBasicAuthServiceProvider::class,
        ];
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }
}
