<?php namespace Olssonm\VeryBasicAuth;

use Illuminate\Support\ServiceProvider;

class VeryBasicAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        // Publishing of configuration
        $this->publishes([
            __DIR__ . '/config.php' => config_path('very_basic_auth.php'),
        ]);

        // Register middleware
        $router->middleware('auth.very_basic', 'Olssonm\VeryBasicAuth\Http\Middleware\VeryBasicAuth');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // If the user doesn't set their own config, load default
        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'very_basic_auth'
        );
    }
}
