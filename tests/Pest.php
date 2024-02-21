<?php

use Illuminate\Support\Facades\Route;
use Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth;
use Olssonm\VeryBasicAuth\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        // Set default config for testing
        config()->set('very_basic_auth.user', 'test');
        config()->set('very_basic_auth.password', 'test');
    
        Route::get('/', fn () => 'ok')->middleware(VeryBasicAuth::class)->name('default');
        Route::get('/test', fn () => 'ok')->middleware(VeryBasicAuth::class);
        Route::get('/inline', fn () => 'ok')->middleware(
            sprintf('auth.very_basic:%s,%s', config('very_basic_auth.user'), config('very_basic_auth.password'))
        )->name('inline');
    })
    ->in(__DIR__);
